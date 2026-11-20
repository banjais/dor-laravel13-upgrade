

@extends('layout.client.container_2021')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home ';
?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $contentData->ticker_title }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title <?=strlen($contentData->ticker_title)>175?'twoline':''?>"> {{ $contentData->ticker_title }}</h3>
                     <div>{!! $contentData->ticker_des !!}</div>
                 </div>
                <!-- include('layout.client.sidebar') -->
            </div>
        </section>
	</div>


@stop
