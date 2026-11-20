@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'धेरै सोधिने प्रश्नहरु ' : 'Frequently Asked Questions';
    $notAvailableMessage = $languageId == 1 ? 'कुनैपनि रेकर्ड पाइएन ।' : 'Records are Not Available.';
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
                        @if (count($faqs) > 0)
                            <div class="accordion" id="accordionExample">
                                @foreach ($faqs as $key=>$faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="{{'heading-'.$key}}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#{{'collapse-'.$key}}" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                {{ $faq->faq_question }}
                                            </button>
                                        </h2>
                                        <div id="{{'collapse-'.$key}}" class="accordion-collapse collapse show"
                                            aria-labelledby="{{'heading-'.$key}}" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <p>{!! $faq->faq_answer !!}</p>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        @else
                            <div>{{ $notAvailableMessage }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>

@stop
