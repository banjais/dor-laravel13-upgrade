@extends('layout.client.container')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $publicationheading = $languageId == 1 ? 'प्रकाशनहरु' : 'Publications';
    $sn = $languageId == 1 ? 'क्र.स' : 'S N';
    $title = $languageId == 1 ? 'शिर्षक' : 'Title';
    $publishDate = $languageId == 1 ? 'प्रकाशित मिति' : 'Published Date';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';

    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $notAvailableMessage = $languageId == 1 ? 'प्रकाशनहरु उपलब्ध छैन ।' : 'Publications are not available.';
    ?>
    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li><a href="{{ $abs_site_url . $subdomain . '/publications' }}">{{ $publicationheading }}</a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $publicationCategory }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ $publicationCategory }} </h3>

                    <?php if(count($publications) > 0) {
						$page = (!empty($_GET['page'])) ?  $_GET['page']: 1;
						$paginate = $publications->perPage();
						$count = ($page-1)*$paginate + 1;
					?>
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="normal_table">
                        <th>{{ $sn }}</th>
                        <th>{{ $title }}</th>
                        <th>{{ $publishDate }}</th>
                        <th>{{ $download }}</th>
                        <?php
                        foreach ($publications as $publication) {
                            echo '<tr>';
                            echo '<td>' . $count++ . '</td>';
                            echo '<td><a href="' . $category_url . '/' . $publication->publication_url . '">' . $publication->publication_heading . '</a></td>';
                            if ($languageId == 1) {
                                echo '<td><i class="far fa-calendar-alt"></i>' . \Calendar::eng_to_nep_dor($publication->published_date) . '</td>';
                            } else {
                                echo '<td><i class="far fa-calendar-alt"></i>' . $publication->en_published_date . '</td>';
                            }

                            echo '<td>';
                            if (file_exists('uploads/publication/' . $publication->publication_attachment) && $publication->publication_attachment != '') {
                                echo '<span><a href="' .
                                    $abs_site_url .
                                    $subdomain .
                                    '/publication/' .
                                    $category_url .
                                    '/force/' .
                                    $publication->publication_url .
                                    '" target="_blank">
                        											<i class="fa fa-download"></i>' .
                                    $download .
                                    '</a>
                        										</span>';
                            } else {
                                echo $fileNotfound;
                            }
                            echo '</td>';

                            echo '</tr>';
                        }
                        ?>
                    </table>
                    </div>
                    <?php echo $publications->links(); ?>
                    <?php } else { ?>
                    <div>{{ $notAvailableMessage }}</div>
                    <?php } ?>
                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
    </div>
@stop
