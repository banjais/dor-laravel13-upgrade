@extends('layout.client.container')
@section('content')

<?php
use \Illuminate\Support\Str;
	$languageId = Session::get('language_id');
?>
   <div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ \Helper::getNameOftext('news', $languageId) }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="">{{ \Helper::getNameOftext('news', $languageId) }}</h3>
                    <div class="">
                    	<?php if(count($newss) > 0){ ?>
						@foreach($newss as $news)

						<?php $newsDate	= ($languageId == 1) ? \Calendar::eng_to_nep_dor($news->published_date) : $news->en_published_date; ?>

							<article class="post sticky hentry post-inner">
								<a href="news/{{ $news->news_url }}"></a>

								<h3 class="hentry__title">
									<a href="news/{{ $news->news_url }}">{{ $news->news_heading }} </a>
								</h3>
								<div class="meta-data">
									<i class="glyphicon glyphicon-calendar mr5"></i>{{ $newsDate }}
								</div>
								<div class="hentry__content">
									<p>
									@if (strlen($news->news_description) != strlen(utf8_decode($news->news_description))) {
									    {{ Str::limit($news->news_description,250,"....") }}
									@else
									     {{ Str::limit($news->news_description,150,"....") }}
									@endif
									</p>
									<p><a href="news/{{ $news->news_url }}" class="more-link">
									<span class="read-more read-more--post">{{ \Helper::getNameOftext('read-more', $languageId) }}</span></a>
									</p>
								</div>
								<div class="clearfix"></div>
							</article>
                          @endforeach

						{{ $newss->links() }}

						<?php } else { ?>
								<div>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</div>
						<?php } ?>
					</div>
                 </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
