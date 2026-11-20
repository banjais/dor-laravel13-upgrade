@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'नियम कानुन' : 'Laws';
    $sn = $languageId == 1 ? 'क्र.स' : 'S N';
    $title = $languageId == 1 ? 'शिर्षक' : 'Title';
    $publishDate = $languageId == 1 ? 'प्रकाशित मिति' : 'Published Date';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $notAvailableMessage = $languageId == 1 ? 'नियम कानुन उपलब्ध छैन ।' : 'Laws are not available.';

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
                            <li class="breadcrumb-item">{{ $heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if (count($laws) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-condensed" id="normal_table">
                                    <th>{{ $sn }}</th>
                                    <th>{{ $title }}</th>
                                    <th>{{ $publishDate }}</th>
                                    <th>{{ $download }}</th>

                                    <?php
                                    $count = 1;
                                    foreach ($laws as $law) {
                                        $lawDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($law->published_date) : $law->en_published_date;
                                        echo '<tr>';
                                        echo '<td>' . $count++ . '</td>';
                                        echo '<td><a href="law/' . $law->law_url . '">' . $law->law_heading . '</a></td>';
                                        echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>' . $lawDate . '</td>';
                                        echo '<td>';
                                        if (file_exists('uploads/law/' . $law->law_attachment) && $law->law_attachment != '') {
                                            echo '<span><a href="' .
                                                '/' .
                                                $subdomain .
                                                '/law/force/' .
                                                $law->law_url .
                                                '" target="_blank">
                                                                        											<i class="glyphicon glyphicon-download mr6"></i>' .
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
                            @else
                                <div class="mt-2">{{ $notAvailableMessage }}</div>
                            @endif
                        </div>

                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>

@stop
