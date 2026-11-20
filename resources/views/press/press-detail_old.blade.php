@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$pressheading	= ($languageId == 1) ? 'प्रेस विज्ञप्ति': 'Press Release';
	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($pressData->published_date) : $pressData->en_published_date ;
?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/presses' }}">{{ $pressheading }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $pressData->press_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated listdetail" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">{{ $pressData->press_heading }}</h3>
                    
                    <div class="col-lg-12">
                    	<p><i class="glyphicon glyphicon-calendar mr5"></i>{{ $publishedDate }}</p>
                    	<br>
                    	<p>{{ $pressData->press_description }}</p>
                        
                        <br>
						<?php 
						if(file_exists("uploads/press/".$pressData->press_attachment) && $pressData->press_attachment!=''){ 
						?>
							<p>{{ $download }}
								<a href="{{ $abs_site_url. $subdomain }}/press/force/{{ $pressData->press_url }}" target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a>
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