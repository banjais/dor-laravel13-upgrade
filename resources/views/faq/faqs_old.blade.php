@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading = ($languageId == 1) ? 'धेरै सोधिने प्रश्नहरु ': 'Frequently Asked Questions';
	$notAvailableMessage	= ($languageId == 1) ? 'कुनैपनि रेकर्ड पाइएन ।': 'Records are Not Available.';
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
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">{{ $heading }}</h3>

					<?php if(count($faqs) > 0){ ?>
                    <div class="panel-group faq" id="panel-718040">
					@foreach($faqs as $faq)
						<div class="panel panel-default ">
						 <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-718040" href="#panel-element-93594{{ $faq->id }}">
							<div class="panel-heading plus">
							{{ $faq->faq_question }}
							</div></a>
							<div id="panel-element-93594{{ $faq->id }}" class="panel-collapse collapse">
								<div class="panel-body">
									<p>{!! $faq->faq_answer !!}</p>
								</div>
							</div>
						</div>
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
