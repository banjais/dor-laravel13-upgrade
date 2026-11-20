@extends('layout.client.container_2021')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$newsDate	= ($languageId == 1) ? \Calendar::eng_to_nep_dor($newsData->published_date) : $newsData->en_published_date;
?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/news' }}">{{ \Helper::getNameOftext('news', $languageId) }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $newsData->news_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated listdetail" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="">{{ $newsData->news_heading }}</h3>

                    <div class="col-lg-12">
                    	<p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $newsDate }}</p>
                    	<br>
                    	<div>{!! $newsData->news_description !!}</div>

                        <br>
						<?php
						if(file_exists("uploads/news/".$newsData->news_attachment) && $newsData->news_attachment!=''){
						?>
                        <p>{{ $download }}<a href="{{ $abs_site_url. $subdomain }}/news/force/{{ $newsData->news_url }}" target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a></p>
						<?php } ?>
                    </div>


                 </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
