@extends('layout.client.container')
@section('content')

<style type="text/css">
	.fancybox-custom .fancybox-skin { box-shadow: 0 0 50px #222; }
</style>
<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'फोटो ग्यालरी': 'Photo Gallery';
	$photos		= ($languageId == 1) ? ' फोटो': ' Photos';
	$notAvailableMessage	= ($languageId == 1) ? 'फोटो ग्यालरी उपलब्ध छैन ।': 'Photo albums are not available.';
?>
   
<div class="container">
	<section class="margin-bottom">
		<div class="breadcrum">
			<ul>
				<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<?php /*<li><a href="">{{ $contentData->content_heading }}</a></li>
				<li><i class="fa fa-chevron-right"></i></li> */ ?>
				<li>{{ $heading }}</li>
			</ul>
        </div>
		  
        <div class="row">
            <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
            <h3 class="hentry__title">{{ $heading }}</h3>
			<div class="row">
			<?php if(count($albums) > 0){ ?>
			@foreach($albums as $album)
             <div class="col-md-6 photogallery">
				<a href="photogallery/{{ $album->album_url }}" title="{{ $album->album_title }}">
					<img src="{{ $abs_site_url }}uploads/album/{{ $album->album_attachment }}" alt="{{ $album->album_title }}" class="img-responsive" />
				</a>
                <a href="photogallery/{{ $album->album_url }}" title="{{ $album->album_title }}">
                <div class="caption">
					<div class="photo_count"><p>{{ $album->count_photos . $photos }}</p></div>
                </div>
                </a>
             </div>
			@endforeach
			
			<?php } else { ?>
				<div>{{ $notAvailableMessage }}</div>
			<?php } ?>
            
	          </div>
	         </div>
             @include('layout.client.sidebar')
            </div>
        <hr class="dotted">
      </section>
</div>
		
@stop