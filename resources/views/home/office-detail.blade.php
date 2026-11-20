@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $pageTitle }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <!-- <h3 class="hentry__title"> </h3> -->
                     <br>
                     <ul class="inside">
                     <?php foreach ($officeData as $key => $office): ?>
					 
						<?php 
						
						if($office->id=='131')
							continue;
						
						?>
                       
                       <li class=""><a href="{{ $abs_site_url . $office->office_subdomain }}">{{ $office->office_name }}</a></li>
					   
					   <?php if($office->id=='79'){?>
						   
						    <li class=""><a href="{{ $abs_site_url . $officeHarakpur[0]->office_subdomain }}">{{ $officeHarakpur[0]->office_name }}</a></li>
						   
					 <?php  } ?>
					   
                     <?php endforeach ?>
                 </ul>
                 </div>
                @include('layout.client.sidebar')
            </div>            
        </section>
	</div>


@stop