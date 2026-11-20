@extends('layout.client.container_2021')
@section('content')

    @php
    use Illuminate\Support\Str;
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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('news', $languageId) }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('news', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if (count($newss) > 0)
                                @foreach ($newss as $news)

                                    @php
                                        $newsDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($news->published_date) : $news->en_published_date;
                                    @endphp

                                    <div class="shadowed-box p-3">
                                        <h6>
                                            <a href="{{ $abs_site_url . $subdomain . '/news/' . $news->news_url }}">{{ $news->news_heading }} </a>
                                        </h6>
                                        <hr/>
                                        <div class="mt-2">
                                            <i class="far fa-calendar-alt"></i> {{ $newsDate }}
                                        </div>
                                        <div class="block-list mt-2">
                                            <p>
                                                @if (strlen($news->news_description) != strlen(utf8_decode($news->news_description)))
                                                    {
                                                    {{ Str::limit($news->news_description, 250, '....') }}
                                                @else
                                                    {{ Str::limit($news->news_description, 150, '....') }}
                                                @endif
                                            </p>
                                            <p><a href="{{ $abs_site_url . $subdomain . '/news/' . $news->news_url }}" class="more-link">
                                                    {{ \Helper::getNameOftext('read-more', $languageId) }}</a>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
                        </div>

                        {{ $newss->links() }}
                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>
@stop
