@extends('layout.client.container')
@section('content')

    <?php
    $languageId = languageId();
    $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($noticeData->published_date) : $noticeData->en_published_date;
    ?>
    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                        </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/notices' }}">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
                    </li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $noticeData->notice_heading }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated listdetail"
                    style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3
                        class="hentry__title <?= strlen($noticeData->notice_heading) > 90 && strlen($noticeData->notice_heading) < 180 ? 'twoline' : (strlen($noticeData->notice_heading) > 180 ? 'threeline' : '') ?> ">
                        {{ $noticeData->notice_heading }}</h3>

                    <div class="col-lg-12">
                        <p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $publishedDate }}</p>
                        <br>
                        <div>{!! $noticeData->notice_description !!}</div>

                        <br>
                        <?php
						if(file_exists("uploads/notice/".$noticeData->notice_attachment) && $noticeData->notice_attachment!=''){
						?>
                        <p>{{ \Helper::getNameOftext('download', $languageId) }}
                            <a href="{{ $abs_site_url . $subdomain }}/notice/force/{{ $noticeData->notice_url }}"
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

@stop
