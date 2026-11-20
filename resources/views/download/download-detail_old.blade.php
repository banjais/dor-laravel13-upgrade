@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$downloadheading	= ($languageId == 1) ? 'डाउनलोड': 'Downloads';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($downloadData->published_date) : $downloadData->en_published_date ;
?>
<div class="container">
    <section class="margin-bottom">
        <div class="breadcrum">
			<ul>
				<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<li><a href="{{ $abs_site_url . $subdomain .'/downloads' }}">{{ $downloadheading }}</a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<li>{{ $downloadData->download_heading }}</li>
			</ul>
        </div>
		<div class="row">
                 <div class="col-md-8 wow fadeInRight animated listdetail" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">{{ $downloadData->download_heading }}</h3>
                    <div class="row">
                    <div class="col-lg-12">
                    	<p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $publishedDate }}</p>
                    	<br>
                    	<div>{{ $downloadData->download_description }}</div>
                        
                        <br>
						<?php 
						if(file_exists("uploads/download/".$downloadData->download_attachment) && $downloadData->download_attachment!=''){ 
						?>
							<p>{{ $download }}<a href="{{ $abs_site_url.$subdomain }}/download/force/{{ $downloadData->download_url }}" target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a></p>
							
						<?php } ?>
                    </div>
                    </div>
                      
                 </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">
            
        </section>
</div>

@stop