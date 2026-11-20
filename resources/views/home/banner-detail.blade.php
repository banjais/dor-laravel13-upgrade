@extends('layout.client.container_2021')

@section('content')
<main class="main-body">
    <section class="content-block">
        <div class="container">
            <div class="breadcrumb-wrap">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url($subdomain) }}">
                                {{ \Helper::getNameOftext('homepage', Session::get('language_id')) }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $banner->banner_title }}
                        </li>
                    </ol>
                </nav>
            </div>

            <article class="banner-detail-content">
                <h2>{{ $banner->banner_title }}</h2>

                @if (!empty($banner->banner_caption))
                    <p class="lead">{{ $banner->banner_caption }}</p>
                @endif

                @if (!empty($banner->banner_attachment))
                    <figure class="mb-4">
                        <img src="{{ asset('uploads/banner/' . $banner->banner_attachment) }}"
                             alt="{{ $banner->banner_title }}"
                             class="img-fluid"
                             style="width: 100%; height: auto; object-fit: contain;" />
                    </figure>
                @endif

                @if (!empty($banner->banner_description))
                    <div class="banner-description">
                        {!! nl2br(e($banner->banner_description)) !!}
                    </div>
                @endif

                <a href="{{ url($subdomain) }}" class="btn btn-secondary mt-4">
                    {{ \Helper::getNameOftext('homepage', Session::get('language_id')) }}
                </a>
            </article>
        </div>
    </section>
</main>
@endsection
