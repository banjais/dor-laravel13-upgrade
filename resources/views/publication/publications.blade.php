@extends('layout.client.container_2021')
@section('content')

    @php
    use App\Models\Office;
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $publicationheading = $languageId == 1 ? 'प्रकाशनहरु' : 'Publications';
    $sn = $languageId == 1 ? 'क्र.स' : 'S N';
    $title = $languageId == 1 ? 'शिर्षक' : 'Title';
    $publishDate = $languageId == 1 ? 'प्रकाशित मिति' : 'Published Date';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';

    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $notAvailableMessage = $languageId == 1 ? 'प्रकाशनहरु उपलब्ध छैन ।' : 'Publications are not available.';

    $heading = $languageId == 1 ? 'प्रकाशन' : 'Publications';
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
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain . '/publications' }}"">{{ $heading }}</a>
                            </li>
                            <li class="breadcrumb-item">{{ $publicationCategory }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $publicationCategory }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if (count($publications) > 0)
                                @php
                                    $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                                    $paginate = $publications->perPage();
                                    $count = ($page - 1) * $paginate + 1;
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed" id="normal_table">
                                        <th>{{ $sn }}</th>
                                        <th>{{ $title }}</th>
                                        <th>{{ $publishDate }}</th>
                                        <th>{{ $download }}</th>
                                        <?php
                                        foreach ($publications as $publication) {
                                            $officeData_x = Office::getOfficeDetailsWithLanguages($publication->office_id, $languageId);
                                            // dd($officeData_x[0]);

                                            echo '<tr>';
                                            echo '<td>' . $count++ . '</td>';
                                            echo '<td><a href="/' . $officeData_x[0]->office_subdomain . '/publication/' . $category_url . '/' . $publication->publication_url . '">' . $publication->publication_heading . '</a>';
                                            echo '<p>';
                                            echo 'Office: <a href="' . $abs_site_url . $officeData_x[0]->office_subdomain. '">'. $officeData_x[0]->office_name . '</a>';
                                            echo '</p>';
                                            echo '</td>';
                                            if ($languageId == 1) {
                                                echo '<td><i class="far fa-calendar-alt"></i> ' . \Calendar::eng_to_nep_dor($publication->published_date) . '</td>';
                                            } else {
                                                echo '<td><i class="far fa-calendar-alt"></i> ' . $publication->en_published_date . '</td>';
                                            }

                                            echo '<td>';
                                            if (file_exists('uploads/publication/' . $publication->publication_attachment) && $publication->publication_attachment != '') {
                                                echo '<span><a href="' .
                                                    '/' .
                                                    $officeData_x[0]->office_subdomain .
                                                    '/publication/' .
                                                    $category_url .
                                                    '/force/' .
                                                    $publication->publication_url .
                                                    '" target="_blank"><i class="fa fa-download"></i>' .
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
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
                        </div>

                        {{ $publications->links() }}
                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>
@stop
