@extends('layout.client.container_2021')
@section('content')
    <?php
    use App\Models\Home;
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $selectDor = $languageId == 1 ? 'कार्यालय छान्नुहोस्' : 'Select DOR Office';
    // dd($url);
    ?>

    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $contentData->content_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row mt-4">
                    <div>
                        <div class="block-title mt-3 mt-md-0">
                            <h2>{{ $contentData->content_heading }}</h2>
                            <div class="divider">
                            </div>
                        </div>
                        <div class="block-list mt-4">
                            {!! $contentData->content_description !!}
                            @php
                                $exclude_download_urls = ['organisation-structure'];
                            @endphp
                            @if (!in_array($url, $exclude_download_urls))
                                @if ($contentData->content_attachment)
                                    @if (file_exists('uploads/pages/' . $contentData->content_attachment))
                                        <p><a href="{{ $abs_site_url . $subdomain }}/page/mainforce/{{ $contentData->content_url }}/{{ $contentData->office_id }}"
                                                target="_blank"><i class="fa fa-download"></i>
                                                {{ \Helper::getNameOftext('download', $languageId) }}</a></p>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
        </section>
    </main>

@stop
