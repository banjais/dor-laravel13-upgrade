@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$publicationheading	= ($languageId == 1) ? 'प्रकाशनहरु': 'Publications';
	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($publicationData->published_date): $publicationData->en_published_date;

    // dd($publicationData);
?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/publications' }}">{{ $publicationheading }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/publication/'. $categoryUrl }}">{{ $publicationCategory }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $publicationData->publication_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated listdetail" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">{{ $publicationData->publication_heading }}</h3>

                    <div class="col-lg-12">
                    	<p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $publishedDate }}</p>
                    	<br>
                    	<div>{!! $publicationData->publication_description !!}</div>

                        <br>
						<?php
						if(file_exists("uploads/publication/".$publicationData->publication_attachment) && $publicationData->publication_attachment!=''){
						?>
                        <p>{{ $download }} <a href="{{ $abs_site_url.$subdomain }}/publication/{{ $categoryUrl.'/force/'.$publicationData->publication_url }}" target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a></p>
						<?php } ?>
                    </div>


                 </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
