@extends('layout.client.container')
@section('content')
<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$selectDor = ($languageId == 1) ? 'कार्यालय छान्नुहोस्': 'Select DOR Office';
?>

<div class="container">
	<section class="margin-bottom">
        <div class="breadcrum">
			<ul>

				<li><i class="fa fa-chevron-right"></i></li>
				<li>{{ $contentData->ticker_title }}</li>
			</ul>
        </div>
		
			<div class="col-md-9 wow fadeInRight animated inside_content" style="visibility: visible; -webkit-animation: fadeInRight;">
				<h3 class="hentry__title">{{ $contentData->ticker_title }}</h3>
			
					<div class="col-md-12 col-sm-12">

					<?php echo $contentData->ticker_title ?>
      

					</div>
		</div>            
	</section>
</div>
<?php 
	
?>


@stop