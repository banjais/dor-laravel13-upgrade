@extends('layout.client.container_2021')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'Search': 'Search';
?>



<div class="container">
    <section class="margin-bottom">
		<div class="breadcrum">
			<ul>
				<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
				<li><i class="fa fa-chevron-right"></i></li>
				<li>{{ $heading }}</li>
			</ul>
        </div>
            <div class="row">
                 <div class="col-md-12 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">Search</h3>

                    <div class="row">
				<div class="col-md-12">
				<script>
  (function() {
    var cx = '010039477917836949310:pu3x0frnyii';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchresults-only></gcse:searchresults-only>

				</div>

			</div>

                 </div>

            </div>
            <hr class="dotted">

        </section>
</div>

@stop
