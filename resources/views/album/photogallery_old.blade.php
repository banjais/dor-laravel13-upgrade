@extends('layout.client.container')
@section('content')

<style type="text/css">
	.fancybox-custom .fancybox-skin { box-shadow: 0 0 50px #222; }
</style>
<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$albums	= ($languageId == 1) ? 'फोटो ग्यालरी': 'Photo Gallery';
	$heading	= ($languageId == 1) ? 'फोटो ग्यालरी': 'Photo Gallery';
	$notAvailableMessage	= ($languageId == 1) ? 'फोटो ग्यालरी उपलब्ध छैन ।': 'Photo galleries are not available.';
?>
   
<div class="container">
    <section class="margin-bottom">
		<div class="breadcrum">
			<ul>
				<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<li><a href="{{ $abs_site_url . $subdomain .'/gallery' }}">{{ $albums }} </a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<li>{{ $albumName }}</li>
			</ul>
        </div>
        <div class="row">
            <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
				<h3 class="hentry__title">{{ $heading }}</h3>
				<div class="row">
			<?php if(count($galleries) > 0){ ?>	
            
				@foreach($galleries as $gallery)
					@if(file_exists('uploads/gallery/'. $gallery->gallery_attachment) && $gallery->gallery_attachment != '')
					<div class="col-md-4">
						<a class="fancybox" href="{{ $abs_site_url }}uploads/gallery/{{ $gallery->gallery_attachment }}" data-fancybox-group="gallery" title="{{ $gallery->gallery_title }}">
							<img src="{{ $abs_site_url }}uploads/gallery/{{ $gallery->gallery_attachment }}" alt="{{ $gallery->gallery_title }}" class="img-responsive" />
						</a>
						</div>
					@endif
				@endforeach	
                </div>			
			
			<?php } else { ?>
				<p>{{ $notAvailableMessage }}</p>
			<?php } ?>	
            
            
          </div>
              @include('layout.client.sidebar')
            </div>
        <hr class="dotted">
      </section>
</div>
		
@stop