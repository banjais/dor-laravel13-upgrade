@extends('layout.client.container')
@section('content')
    <?php

    $languageId = Session::get('language_id');
    $home = ($languageId == 1) ? 'गृह पृष्‍ठ  ' : 'Home';
    $circularheading = ($languageId == 1) ? 'सुचना' : 'Circulars';
    $fileNotfound = ($languageId == 1) ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $download = ($languageId == 1) ? 'डाउनलोड गर्नुहोस्' : 'Download';
    ?>

    @if($circularData)
        <?php

        $publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($circularData->published_date) : $circularData->en_published_date;
        ?>
        <div class="container">
            <section class="margin-bottom">
                <div class="breadcrum">
                    <ul>
                        <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                        <li><i class="fa fa-chevron-right"></i></li>
                        <li><a href="{{ $abs_site_url . $subdomain .'/circulars' }}">{{ $circularheading }}</a></li>
                        <li><i class="fa fa-chevron-right"></i></li>
                        <li>{{ $circularData->circular_heading }}</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-8 wow fadeInRight animated listdetail"
                         style="visibility: visible; -webkit-animation: fadeInRight;">
                        <h3 class="hentry__title">{{ $circularData->circular_heading }}</h3>

                        <div class="col-lg-12">
                            <p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $publishedDate }}</p>
                            <br>
                            <div>{!! $circularData->circular_description !!}</div>

                            <br>
                            <?php
                            if(file_exists("uploads/circular/" . $circularData->circular_attachment) && $circularData->circular_attachment != ''){
                            ?>
                            <p>{{ $download }}
                                <a href="{{ $abs_site_url. $subdomain }}/circular/force/{{ $circularData->circular_url }}"
                                   target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a>
                            </p>

                            <?php } ?>
                        </div>


                    </div>
                    @include('layout.client.sidebar')
                </div>
                <hr class="dotted">

            </section>
        </div>

    @else

        <div class="container">
            <section class="margin-bottom">
                <div class="breadcrum">
                    <ul>
                        <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                        <li><i class="fa fa-chevron-right"></i></li>
                        <li><a href="{{ $abs_site_url . $subdomain .'/circulars' }}">{{ $circularheading }}</a></li>

                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-8 wow fadeInRight animated listdetail"
                         style="visibility: visible; -webkit-animation: fadeInRight;">
                        <h3 class="hentry__title"> {{ $fileNotfound }}</h3>

                        <div class="col-lg-12">
                            {{ $fileNotfound }}
                        </div>


                    </div>
                    @include('layout.client.sidebar')
                </div>
                <hr class="dotted">

            </section>
        </div>

    @endif

@stop
