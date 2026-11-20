@extends('layout.client.container')
@section('content')

    <?php
    use App\Models\Office;
    $languageId = languageId();
    ?>
    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}</a>
                    </li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    @if (isset($office_name))
                        <li><a
                                href="{{ $abs_site_url . $subdomain . '/notices' }}">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
                        </li>
                    @else
                        <li>{{ \Helper::getNameOftext('notices', $languageId) }}</li>
                    @endif
                </ul>

            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ \Helper::getNameOftext('notices', $languageId) }}
                        {{ isset($office_name) ? ' :- ' . $office_name : '' }}</h3>

                    <form action="" method="POST" id="livesearch">
                        <p>
                            <input type="text" name="office_name" class="input-text auto" placeholder=" Office Name" />
                            {{ \Helper::getNameOftext('date-from', $languageId) }} <input name="from"
                                class="date-picker" />
                            {{ \Helper::getNameOftext('date-to', $languageId) }} <input name="to" class="date-picker" />
                            <input type="submit" value="Search" name="submit" class="btn" />
                            <br />
                        </p>
                    </form>

                    <?php
		if(count($notices) > 0) {
			$page = (!empty($_GET['page'])) ?  $_GET['page']: 1;
			$paginate = $notices->perPage();
			$count = ($page-1)*$paginate + 1;
	?>
                    <table class="roadproj table-striped table-condensed" id="normal_table">
                        <thead>
                            <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                            <th width="50%">{{ \Helper::getNameOftext('title', $languageId) }}</th>
                            <th>{{ \Helper::getNameOftext('office', $languageId) }}</th>
                            <th>{{ \Helper::getNameOftext('published-date', $languageId) }}</th>
                            <th>{{ \Helper::getNameOftext('download', $languageId) }}</th>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($notices as $notice) {
                                $officeData = Office::getOfficeDetailsWithLanguages($notice->office_id, $languageId);
                                echo '<tr>';
                                echo '<td>' . $count++ . '</td>';
                                echo '<td><a target="_blank" href="' . route('notice.detail.slug', ['subdomain' => $officeData[0]->office_subdomain, 'url' => $notice->notice_url]) . '">' . $notice->notice_heading . '</a></td>';
                                if ($languageId == 1) {
                                    echo '<td><a href="' . $abs_site_url . $officeData[0]->office_subdomain . '">' . $officeData[0]->office_name . '</a></td>';
                                } else {
                                    echo '<td><a href="' . $abs_site_url . $officeData[0]->office_subdomain . '">' . $officeData[1]->office_name . '</a></td>';
                                }

                                if ($languageId == 1) {
                                    echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>' . \Calendar::eng_to_nep_dor($notice->published_date) . '</td>';
                                } else {
                                    echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>' . $notice->en_published_date . '</td>';
                                }

                                echo '<td>';
                                if (file_exists('uploads/notice/' . $notice->notice_attachment) && $notice->notice_attachment != '') {
                                    echo '<span><a href="' .
                                        $abs_site_url .
                                        $notice->office_subdomain .
                                        '/notice/force/' .
                                        $notice->notice_url .
                                        '" target="_blank">
                            											<i class="glyphicon glyphicon-download mr6"></i>' .
                                        \Helper::getNameOftext('download', $languageId) .
                                        '</a>
                            										</span>';
                                } else {
                                    echo \Helper::getNameOftext('file-not-found', $languageId);
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php echo $notices->links(); ?>
                    <?php } else { ?>
                    <div>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</div>
                    <?php } ?>

                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
    </div>
    <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(function() {
            //autocomplete
            $(".auto").autocomplete({
                source: "{{ $abs_site_url }}home/searchoffice",
                minLength: 1,


            });
            // debugger;

        });
    </script>
@stop
