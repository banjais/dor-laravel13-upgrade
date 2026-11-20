@extends('layout.client.container')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $lawheading = $languageId == 1 ? 'नियम कानुन' : 'Laws';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $lawDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($lawData->published_date) : $lawData->en_published_date;
    ?>
    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li><a href="{{ $abs_site_url . $subdomain . '/laws' }}">{{ $lawheading }}</a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $lawData->law_heading }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated listdetail"
                    style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ $lawData->law_heading }}</h3>

                    <div class="col-lg-12">
                        <p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $lawDate }}</p>
                        <br>
                        <div>{!! $lawData->law_description !!}</div>

                        <br>
                        <?php
						if(file_exists("uploads/law/".$lawData->law_attachment) && $lawData->law_attachment!=''){
						?>
                        <p>{{ $download }}<a href="{{ $abs_site_url . $subdomain }}/law/force/{{ $lawData->law_url }}"
                                target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a></p>
                        <?php } ?>
                    </div>


                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
    </div>

@stop
