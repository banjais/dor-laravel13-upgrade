@extends('layout.client.container')
@section('content')

    <?php
    use App\Models\Office;
    use App\Models\Circular;
    $languageId = Session::get('language_id');
    $officeId = Office::getIdfromSubdomain($subdomain);
    $circulars = Circular::getMainHomePageCirculars($officeId, $languageId);
    ?>

    <section class="wrap-dark-color margin-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12 feature-container">
                    <a href="http://eproc.dor.gov.np/" class="feature-icon first-icon" target="_blank">
                        <i class="fa fa-users"></i>
                        <h4>{{ \Helper::getNameOftext('eproc', $languageId) }}</h4>
                        <!--<h4>E-Procument Section</h4>-->
                    </a>
                    <a href="http://dryicesolutions.net/grms/" class="feature-icon second-icon" target="_blank">
                        <i class="fa fa-dashboard second"></i>
                        <h4>{{ \Helper::getNameOftext('grms', $languageId) }}</h4>
                        <!--<h4>गुनासो ब्यवस्थापन प्रणाली </h4>-->
                    </a>
                    <a href="http://armp.aviyaan.com/bms/" class="feature-icon third-icon" target="_blank">
                        <i class="fa fa-align-justify third"></i>
                        <h4>{{ \Helper::getNameOftext('bms', $languageId) }}</h4>
                        <!--<h4>पुल ब्यबस्थापन प्रणाली </h4>-->
                    </a>
                    <a href="https://ssrn.dor.gov.np/national"
                        class="feature-icon forth-icon" target="_blank">
                        <i class="fa fa-road forth"></i>
                        <h4>{{ \Helper::getNameOftext('ssrn', $languageId) }}</h4>
                    </a>
                    <a href="http://eis.dor.gov.np/main/public_home" class="feature-icon forth-icon" target="_blank">
                        <i class="fa fa-car forth"></i>
                        <h4>{{ \Helper::getNameOftext('eis', $languageId) }}</h4>
                    </a>
                    <a href="http://armp.aviyaan.com/" class="feature-icon forth-icon" target="_blank">
                        <i class="fa fa-building forth"></i>
                        <h4>{{ \Helper::getNameOftext('armp', $languageId) }}</h4>
                    </a>
                    <a href="http://fmis.dor.gov.np/home/" class="feature-icon forth-icon" target="_blank">
                        <i class="fa fa-cubes forth"></i>
                        <h4>{{ \Helper::getNameOftext('fmis', $languageId) }}</h4>
                    </a>
                    <a href="http://cms.dor.gov.np/home/login" class="feature-icon forth-icon" target="_blank">
                        <i class="fa fa-flag forth"></i>
                        <h4>{{ \Helper::getNameOftext('cms', $languageId) }}</h4>
                    </a>
                    <a class="feature-icon last-icon" href="{{ $abs_site_url . $subdomain }}/getdistancecalc">
                        <i class="fa fa-calculator last"></i>
                        <h4>{{ \Helper::getNameOftext('distance-calculator', $languageId) }}</h4>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <section class="margin-bottom">
            <div class="row">
                <div class="col-md-8  fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight; ">
                    <section class="carousel-section">

                        <?php if(count($banners) > 0){ ?>
                        <div id="carousel-example-generic" class="carousel carousel-razon slide" data-ride="carousel"
                            data-interval="5000">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <?php

                                $count = 0;
                                foreach ($banners as $banner) {
                                    if (file_exists('uploads/banner/' . $banner->banner_attachment) && $banner->banner_attachment != '') {
                                        $class = $count == 0 ? 'active' : '';
                                        echo '<li data-target="#carousel-example-generic" data-slide-to="' . $count . '" class="' . $class . '"></li>';
                                    }
                                    $count++;
                                }
                                ?>
                            </ol>

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <?php
									$count = 1;
									foreach($banners as $banner) {
										if(file_exists('uploads/banner/'.$banner->banner_attachment) && $banner->banner_attachment != ''){
											$class = ($count == 1) ? 'item active' : 'item';
									?>
                                <div class="{{ $class }}">
                                    <div class="">
											<div class=" row">
                                        <div class="col-md-12 col-sm-12 carousel-img-wrap">
                                            <div class="carousel-img">
                                                <img src="{{ $rel_site_url }}uploads/banner/{{ $banner->banner_attachment }}"
                                                    class="img-responsive animated right animation-delay-1"
                                                    style="max-height: 487px; height:487px; " alt="Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
										$count++;
										}
									}
									?>
                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </a>
                </div>
                <?php } else { ?>
                <div id="carousel-example-generic" class="carousel carousel-razon slide" data-ride="carousel"
                    data-interval="5000">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">

                        <div class="item active">
                            <div class="">
											<div class=" row">
                                <div class="col-md-12 col-sm-12 carousel-img-wrap">
                                    <div class="carousel-img">
                                        <img src="{{ $abs_site_url }}assets/img/banner.png"
                                            class="img-responsive animated bounceInRight animation-delay-1" alt="Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php } ?>
        </section> <!-- carousel -->
        <div class="col-md-12 wow fadeInRight animated margin-bottom"
            style="visibility: visible; -webkit-animation: fadeInRight; padding:0;">

        </div>

        <div class="margin-bottom" style="clear:both;">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-ar">
                <li class="active"><a href="#notices"
                        data-toggle="tab">{{ \Helper::getNameOftext('notices', $languageId) }}</a></li>
                <li><a href="#news" data-toggle="tab">{{ \Helper::getNameOftext('news', $languageId) }}</a></li>
                <li><a href="#publications"
                        data-toggle="tab">{{ \Helper::getNameOftext('publications', $languageId) }}</a></li>
                <li><a href="#tenders" data-toggle="tab">{{ \Helper::getNameOftext('tenders', $languageId) }}</a></li>
                <li><a href="#downloads" data-toggle="tab">{{ \Helper::getNameOftext('circulars', $languageId) }}</a>
                </li>
            </ul>

            <!-- Tab panes -->

            <div class="tab-content">
                <div class="tab-pane " id="tenders">
                    <?php if(count($tenders) > 0) { ?>
                    <ul class="media-list">
                        @foreach ($tenders as $tender)
                            <li class="media row">
                                <div class="media-body">
                                    <p class="media-heading"><a
                                            href="{{ $subdomain }}/tender/{{ $tender->tender_url }}">{{ $tender->tender_heading }}</a>
                                    </p>
                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($tender->published_date) : $tender->en_published_date; ?>
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <a
                        href="{{ $abs_site_url . $subdomain }}/tenders">{{ \Helper::getNameOftext('view-more', $languageId) }}</a>
                    <?php } else {
								echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
							}?>
                </div>
                <div class="tab-pane" id="news">
                    <?php if(count($homepagenews) > 0) {?>
                    <ul class="media-list">
                        @foreach ($homepagenews as $news)
                            <li class="media row">
                                <div class="media-body">
                                    <p class="media-heading"><a
                                            href="{{ $subdomain }}/news/{{ $news->news_url }}">{{ $news->news_heading }}</a>
                                    </p>
                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($news->published_date) : $news->en_published_date; ?>
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <a
                        href="{{ $abs_site_url . $subdomain }}/news">{{ \Helper::getNameOftext('view-more', $languageId) }}</a>
                    <?php } else {
								echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
							}?>
                </div>

                <div class="tab-pane" id="publications">
                    <?php if(count($publications) > 0) { ?>
                    <ul class="media-list">
                        @foreach ($publications as $publicationCategory)
                            @foreach ($publicationCategory->details as $detail)
                            @endforeach
                            <li class="media row">
                                <div class="media-body">
                                    <p class="media-heading"><a
                                            href="{{ $abs_site_url . $subdomain }}/publication/{{ $publicationCategory->category_url }}">{{ $detail->category_heading }}</a>
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a
                        href="{{ $abs_site_url . $subdomain }}/publications">{{ \Helper::getNameOftext('view-more', $languageId) }}</a>
                    <?php } else {
							echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
						}?>
                </div>

                <div class="tab-pane active" id="notices">
                    <?php if(count($notices) > 0) { ?>
                    <ul class="media-list">
                        @foreach ($notices as $notice)
                            <li class="media row">
                                <div class="media-body">
                                    <p class="media-heading">
                                        <a
                                            href="{{ $subdomain }}/notice/{{ $notice->notice_url }}">{{ $notice->notice_heading }}</a>
                                    </p>
                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($notice->published_date) : $notice->en_published_date; ?>
                                    </small>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                    <a
                        href="{{ $abs_site_url . $subdomain }}/notices">{{ \Helper::getNameOftext('view-more', $languageId) }}</a>
                    <?php } else {
							echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
						}?>
                </div>
                <div class="tab-pane" id="downloads">
                    <?php if(count($circulars) > 0) { ?>
                    <ul class="media-list">
                        @foreach ($circulars as $circular)
                            <li class="media">
                                <div class="media-body">
                                    <p class="media-heading"><a
                                            href="{{ $abs_site_url . $subdomain }}/circular/{{ $circular->circular_url }}">{{ $circular->circular_heading }}</a>
                                    </p>
                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($circular->published_date) : $circular->en_published_date; ?>
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a
                        href="{{ $abs_site_url . $subdomain }}/circulars">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>

                    <?php } else {
                echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
              }?>
                </div>
            </div>
        </div>
    </div>
    @include('layout.client.sidebar')
    <!--select dor office-->
    </div>
    </section>
    </div>
@stop
