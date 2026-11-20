@extends('layout.client.container')
@section('content')
<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$projectheading	= ($languageId == 1) ? 'रोड प्रोजेक्टस': 'Road Projects';
?>
	<style>#gmap_canvas img{max-width:none!important;background:none!important}</style>


        <script src="https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg"></script>
		<script type="text/javascript">
		var latitude = '<?php echo $projectData->project_latitude; ?>';
		var longitude = '<?php echo $projectData->project_longitude; ?>';
		var zoom = '<?php echo $projectData->project_zoom; ?>';
		var name = '<?php echo $projectData->project_heading; ?>';
		var address = '<?php echo $projectData->project_location; ?>';

		function init_map(){
			var myOptions = {zoom:10,center:new google.maps.LatLng(latitude, longitude),mapTypeId: google.maps.MapTypeId.ROADMAP};
			map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
			marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(latitude, longitude)});
			infowindow = new google.maps.InfoWindow({content:"<b>"+ name +" </b><br/>"+ address });
			google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});
			infowindow.open(map,marker);
		}
		google.maps.event.addDomListener(window, 'load', init_map);
		</script>

	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/projects' }}">{{ $projectheading }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $projectData->project_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title"> {{ $projectData->project_heading }}</h3>


					<div id="gmap_canvas" style="height:400px;width:100%;"></div>
                     <div>{!! $projectData->project_description !!}</div>
                 </div>
               @include('layout.client.sidebar')
            </div>
        </section>
	</div>


@stop
