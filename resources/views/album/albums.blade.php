@extends('layout.client.container_2021')
@section('content')

    <style type="text/css">
        .fancybox-custom .fancybox-skin {
            box-shadow: 0 0 50px #222;
        }

    </style>
    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'फोटो ग्यालरी' : 'Photo Gallery';
    $photos = $languageId == 1 ? ' फोटो' : ' Photos';
    $notAvailableMessage = $languageId == 1 ? 'फोटो ग्यालरी उपलब्ध छैन ।' : 'Photo albums are not available.';

    $bandwidth_code = Session::get('bandwidth_code', 'high');
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
                        </div>
                        <div class="row">
                            @if (count($albums) > 0)
                                @foreach ($albums as $album)
                                    <div class="col-md-3">
                                        <div class="media-card shadowed-box p-3">
                                            <div class="fig-wrap zoom-effect">
                                                <a href="photogallery/{{ $album->album_url }}"
                                                    title="{{ $album->album_title }}">
                                                    @if ($bandwidth_code == 'high')
                                                        <img src="{{ $abs_site_url }}uploads/album/{{ $album->album_attachment }}"
                                                            class="img-responsive" alt="{{ $album->album_title }}" />
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="fig-caption">
                                                <p><a href="photogallery/{{ $album->album_url }}">{{ $album->album_title }}
                                                        - {{ $album->count_photos . $photos }}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>{{ $notAvailableMessage }}</p>
                            @endif
                        </div>
                    </div>
        </section>
    </main>

@stop
