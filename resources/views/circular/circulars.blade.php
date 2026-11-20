@extends('layout.client.container_2021')
@section('content')

    @php
    use App\Models\Office;
    $languageId = Session::get('language_id');
    @endphp

    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            @if (isset($office_name))
                                <li class="breadcrumb-item"><a
                                        href="{{ $abs_site_url . $subdomain . '/circulars' }}">{{ \Helper::getNameOftext('circulars', $languageId) }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item">{{ \Helper::getNameOftext('circulars', $languageId) }}</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                @if (!$auth)
                    <div class="block-title">
                        <h2>{{ \Helper::getNameOftext('circulars', $languageId) }}</h2>
                        <div class="divider"></div>
                        <div class="mt-4">
                            You should have office login privileges to view the circulars. <a
                                href="https://dor.gov.np/admin">Office Login</a>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-4">
                                <div class="block-title">
                                    <h2>{{ \Helper::getNameOftext('circulars', $languageId) }}</h2>
                                    <div class="divider"></div>
                                </div>
                                <div class="form-wrap">
                                    <form action="" method="GET" id="livesearch">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-6">
                                                <div class="form-group">
                                                    <label
                                                        for="office_name">{{ \Helper::getNameOftext('office', $languageId) }}</label>
                                                    <input type="text" name="office_name"
                                                        class="input-text auto form-control" placeholder=" Office Name"
                                                        value="{{ Request::get('office_name') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6 col-lg-2">
                                                <div class="form-group">
                                                    <label
                                                        for="">{{ \Helper::getNameOftext('date-from', $languageId) }}</label>
                                                    <input name="from" class="date-picker form-control"
                                                        value="{{ Request::get('from') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6 col-lg-2">
                                                <div class="form-group">
                                                    <label
                                                        for="">{{ \Helper::getNameOftext('date-to', $languageId) }}</label>
                                                    <input name="to" class="date-picker form-control"
                                                        value="{{ Request::get('to') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mt-4">
                                                    <input type="submit" value="Search" class="submit-btn w-100" />
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                @if (count($circulars) > 0)
                                    @php
                                        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                                        $paginate = $circulars->perPage();
                                        $count = ($page - 1) * $paginate + 1;
                                    @endphp
                                @endif
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-condensed" id="normal_table">
                                    <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                    <th width="50%">{{ \Helper::getNameOftext('title', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('office', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('published-date', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('download', $languageId) }}</th>

                                    <?php

                                    foreach ($circulars as $index => $circular) {
                                        echo '<tr>';
                                        echo '<td>' . $count++ . '</td>';
                                        echo '<td><a href="circular/' . $circular->circular_url . '">' . $circular->circular_heading . '</a></td>';
                                        $officeName = Office::getOfficeDetailsWithLanguages($circular->office_id, $languageId);
                                        if ($languageId == 1) {
                                            echo '<td><a href="' . $abs_site_url . $officeName[0]->office_subdomain . '">' . $officeName[0]->office_name . '</a></td>';
                                        } else {
                                            echo '<td><a href="' . $abs_site_url . $officeName[0]->office_subdomain . '">' . $officeName[1]->office_name . '</a></td>';
                                        }

                                        if ($languageId == 1) {
                                            echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>' . \Calendar::eng_to_nep_dor($circular->published_date) . '</td>';
                                        } else {
                                            echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>' . $circular->en_published_date . '</td>';
                                        }

                                        echo '<td>';
                                        if (file_exists('uploads/circular/' . $circular->circular_attachment) && $circular->circular_attachment != '') {
                                            echo '<span><a href="' .
                                                $abs_site_url .
                                                $subdomain .
                                                '/circular/force/' .
                                                $circular->circular_url .
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
                                </table>
                            </div>
                            @if (count($circulars) == 0)
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
                            {{ $circulars->appends($_GET)->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </section>
    </main>
    <link rel="stylesheet" href="{{ asset('assets/css_2021/ui-lightness/jquery-ui-1.10.4.custom.min.css') }}"
        type="text/css" />
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js_2021/jquery-ui-1.10.4.custom.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            //autocomplete
            $(".auto").autocomplete({
                source: "{{ $abs_site_url }}home/searchoffice",
                minLength: 1,
            });
            // date picker
            $(".date-picker").datepicker({
                dateFormat: 'yy-mm-dd'
            });

        });
    </script>
@endsection
@stop
