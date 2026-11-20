@extends('layout.client.container_2021')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <style type="text/css">
        .fancybox-custom .fancybox-skin {
            box-shadow: 0 0 50px #222;
        }

    </style>
    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $albums = $languageId == 1 ? 'फोटो ग्यालरी' : 'Photo Gallery';
    $heading = $languageId == 1 ? 'फोटो ग्यालरी' : 'Photo Gallery';
    $notAvailableMessage = $languageId == 1 ? 'फोटो ग्यालरी उपलब्ध छैन ।' : 'Photo galleries are not available.';

    $bandwidth_code = Session::get('bandwidth_code', 'high');
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
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain . '/gallery' }}">{{ $albums }}</a>
                            </li>
                            <li class="breadcrumb-item">{{ $albumName }}
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
                        </div>
                        <div class="row">
                            @if (count($galleries) > 0)
                                @foreach ($galleries as $gallery)
                                    <div class="col-md-3">
                                        <div class="media-card shadowed-box p-3">
                                            <div class="fig-wrap  zoom-effect">
                                                <a data-fancybox="gallery-a"
                                                    href="{{ $abs_site_url }}uploads/gallery/{{ $gallery->gallery_attachment }}"
                                                    data-caption="{{ $gallery->gallery_caption }}">
                                                    @if ($bandwidth_code == 'high')
                                                        <img src="{{ $abs_site_url }}uploads/gallery/{{ $gallery->gallery_attachment }}"
                                                            class="img-responsive" alt="{{ $gallery->gallery_title }}" />
                                                    @endif
                                                </a>
                                            </div>
                                            @if ($gallery->gallery_title)
                                                <div>{{ $gallery->gallery_title }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>{{ $notAvailableMessage }}</p>
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
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@stop
