@extends('layout.client.container')
@section('content')
    <?php
    use App\Models\Office;
    use Illuminate\Support\Str;

    $languageId = Session::get('language_id');
    $officeId = Office::getIdfromSubdomain($subdomain);
    ?>
    <!-- <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.2.min.js"></script> -->
    <!--Loader-->

    <div class="container">
        <div class="ticker-container">
            <div class="ticker-caption">
                Latest News
            </div>
            <ul id="myUl">
                <?php foreach ($homepagenewstickers as $homepagenewsticker){?>
                <li>
                    <a
                        href="{{ url('home/latest-news', [$homepagenewsticker->ticker_id]) }}">{{ $homepagenewsticker->ticker_title }}</a>
                </li><?php }?>
            </ul>
        </div>

    </div>
    <div class=" hhh " style="display: none;">
        <div class="b-label">

            <label id="lblheadline">Latest Update</label>
        </div>
        <div class="ticker">
            <ul id="divbreakingnews">
                <li><a href="#">The atmosphere in Chania has a touch of Florence and Venice.</a></li>
                <li><a href="#">Beatiful flowers in Kolymbari, Crete.</a></li>
                <li><a href="#">The atmosphere in Chania has a touch of Florence and Venice.</a></li>
                <li><a href="#">Beatiful flowers in Kolymbari, Crete.</a></li>
            </ul>
        </div>
    </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#myUl").endlessRiver();
        });
    </script>
    <style type="text/css">
        /* liScroll styles */

        /* liScroll styles */
        #myUl a {
            color: #000;
        }

        #myUl a:hover {
            color: #D91D24;
            text-decoration: none;
        }

        .ticker-caption {
            background: #008AC6;
            padding: 10px;
            float: left;
            z-index: 999;
            position: absolute;
            color: #fff;
        }

        #ER_1481793820121 {
            float: left;
            position: absolute;
            width: 84.55%;
            overflow: hidden;
            padding: 0 10px 0 0;
            background: #F0F0F0;
        }

        .tickercontainer {
            /* the outer div with the black border */
            width: 100%;
            min-height: 50px;
            margin: 0;
            padding: 0;
            overflow: hidden;
            text-align: center;
        }

        /* that serves as a mask. so you get a sort of padding both left and right */
        .tickercontainer .mask {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        ul.newsticker {
            /* that's your list */
            position: relative;
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;

        }

        ul.newsticker li:after {
            content: "|";
        }

        /* important: display inline gives incorrect results when you check for elem's width */
        ul.newsticker li {
            float: left;
            margin: 10px;
            padding: 0;
        }

        ul.er-controls {
            list-style: none;
            display: inline-block;
            padding: 0;
        }

        ul.er-controls>li {
            display: inline-block;
            padding: 0 10px;
            background: #f0f0f0;
            margin: 5px;
            border-radius: 5px;
            height: 40px;
            line-height: 40px;
        }

        ul.er-controls>li:hover {
            background: #f5f5f5;
            cursor: pointer;
        }

        @media(max-width: 768px) {
            .ticker-caption {
                width: 100%;
                /*position: absolute;*/
                /*float: left;*/
            }

            .ticker-container #myUl {
                height: 50px;
                overflow: hidden;
            }

            .carousel-inner .caption>h3 {
                bottom: -43px;
            }

            .wrap-dark-color {
                background: #333;
            }

            .navbar-nav {
                margin: 0 -51px 0 0;
            }
        }

        @media (min-width:1024px) and (max-width: 1180px) {

            .logotext {
                width: 70%;
            }

        }

    </style>

    <div class="container">

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
                                        <img src="{{ asset('uploads/banner/' . $banner->banner_attachment) }}"
                                            class="img-responsive animated right animation-delay-1" alt="Image">
                                    </div>
                                    <div class="caption">
                                        <h3 style="color: #fff;">{{ $banner->banner_caption }}</h3>
                                        <!-- <p>The atmosphere in Chania has a touch of Florence and Venice.</p> -->
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

    </div>

    <!-- Tools -->
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
                        <h4>{{ \Helper::getNameOftext('ssrn-shortcut', $languageId) }}</h4>
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
                <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <div class="row">

                        <div class="col-md-6 introduct">

                            <?php

  if($homeData2 != ''){

  ?>

                            <div class="thumbnail pmmes">
                                <h3 class="no-margin-top hentry__title">{{ $homeData2->home_heading }}</h3>
                                <a href="{{ $abs_site_url . $subdomain }}" style="width:100px;">

                                    @if (file_exists('uploads/pages/' . $homeData2->home_attachment) && $homeData2->home_attachment != '')
                                        <img src="{{ asset('uploads/pages/' . $homeData2->home_attachment) }}">
                                    @endif
                                </a>

                                <p>
                                    <?php
                                    if (strlen($homeData2->home_description) != strlen(utf8_decode($homeData2->home_description))) {
                                        echo Str::limit($homeData2->home_description, 250, '....');
                                    } else {
                                        echo Str::limit($homeData2->home_description, 150, '....');
                                    }

                                    ?>
                                </p>
                            </div>
                            <?php } else { ?>
                            <h3 class="no-margin-top">{{ \Helper::getNameOftext('director-general', $languageId) }}</h3>
                            <div class="thumbnail pmmes">
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            </div>
                            <?php }


      $link = false;
  ?>

                        </div>

                        <!--welcome to dor-->
                        <div class="col-md-6 welcome">
                            @if (isset($homeData1) && $homeData1 != '')
                                <h3 class="no-margin-top hentry__title">{{ $homeData1->home_heading }} </h3>

                                <div>
                                    <?php
                                    $link = false;
                                    if (strlen($homeData1->home_description) != strlen(utf8_decode($homeData1->home_description))) {
                                        //echo 'String uses multi-byte chars (is not English)';
                                        if (strlen($homeData1->home_description) > 499) {
                                            $link = true;
                                        }
                                        echo Str::limit($homeData1->home_description, 500, '....');
                                    } else {
                                        //echo 'String is ASCII (probably English)';
                                        if (strlen($homeData1->home_description) > 499) {
                                            $link = true;
                                        }

                                        echo Str::limit($homeData1->home_description, 500, '....');
                                    }
                                    ?>

                                </div>
                            @else
                                <h3 class=""></h3>
                                <div>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</div>
                            @endif
                            @if ($link == true)
                                <p style="
                                            text-align:right;" class="btn float-right"><a
                                        href="{{ $abs_site_url . $subdomain . '/home/' . $homeData1->home_url }}">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
                                </p>
                            @endif

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 banners">


                            <h3 class="hentry__title">{{ $homespokeperson->home_heading }} </h3>

                            {!! $homespokeperson->home_description !!}

                        </div>

                        <div class="col-md-6 home_tab">

                            <?php if($subdomain == 'home' || Route::currentRouteAction()!='HomeController@getIndex' ){ ?>
                            <ul class="nav nav-tabs nav-tabs-ar">
                                <li class="active"><a href="#notices"
                                        data-toggle="tab">{{ \Helper::getNameOftext('notices', $languageId) }}</a></li>
                                <li><a href="#news" data-toggle="tab"> {{ \Helper::getNameOftext('news', $languageId) }}
                                    </a></li>
                                <li><a href="#publications"
                                        data-toggle="tab">{{ \Helper::getNameOftext('publications', $languageId) }}</a>
                                </li>
                                <li><a href="#circulars" data-toggle="tab">
                                        {{ \Helper::getNameOftext('circulars', $languageId) }} </a></li>
                            </ul>
                            <div class="tab-content">

                                <div class="tab-pane active" id="notices">
                                    <?php if(count($notices) > 0) { ?>
                                    <ul class="media-list">
                                        @foreach ($notices as $notice)
                                            <?php
                                            $officeData = Office::getOfficeDetailsWithLanguages($notice->office_id, $languageId);
                                            ?>
                                            <li class="media row">
                                                <div class="media-body">
                                                    <p class="media-heading">
                                                        <a target="_blank"
                                                            href="{{ route('notice.detail.slug', ['subdomain' => $officeData[0]->office_subdomain, 'url' => $notice->notice_url]) }}">{{ $notice->notice_heading }}</a>
                                                    </p>
                                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($notice->published_date) : $notice->en_published_date; ?><br>
                                                        Office :
                                                        <?php
                                                        if ($languageId == 1) {
                                                            echo '<td><a href="' . $abs_site_url . $officeData[0]->office_subdomain . '">' . $officeData[0]->office_name . '</a></td>';
                                                        } else {
                                                            echo '<td><a href="' . $abs_site_url . $officeData[0]->office_subdomain . '">' . $officeData[1]->office_name . '</a></td>';
                                                        }
                                                        ?> <br>
                                                    </small>

                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                    <a
                                        href="{{ $abs_site_url . $subdomain }}/notices">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>

                                    <?php } else {
                echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
              } ?>

                                </div>
                                <div class="tab-pane" id="news">
                                    <?php if(count($sidebarnews) > 0) { ?>
                                    <ul class="media-list">
                                        @foreach ($sidebarnews as $news)
                                            <li class="media row">
                                                <div class="media-body">
                                                    <p class="media-heading"><a
                                                            href="{{ $abs_site_url . $subdomain }}/news/{{ $news->news_url }}">{{ $news->news_heading }}</a>
                                                    </p>
                                                    <small>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                                        <?php echo $languageId == 1 ? \Calendar::eng_to_nep_dor($news->published_date) : $news->en_published_date; ?>
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a
                                        href="{{ $abs_site_url . $subdomain }}/news">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>

                                    <?php } else {
                echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
              } ?>
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
                                        href="{{ $abs_site_url . $subdomain }}/publications">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>

                                    <?php } else {
                echo '<div>'. \Helper::getNameOftext('record-not-found', $languageId) .'</div>';
              } ?>

                                </div>

                                <div class="tab-pane " id="circulars">
                                    <?php if(count($circulars) > 0) { ?>
                                    <ul class="media-list">
                                        @foreach ($circulars as $circular)
                                            <li class="media row">
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
              } ?>

                                </div>


                            </div>
                            <?php } ?>
                        </div>
                        <!--select office-->
                        <!--banners-->


                    </div>
                </div>
                @include('layout.client.sidebar')
            </div>
        </section>
    </div>


@stop
