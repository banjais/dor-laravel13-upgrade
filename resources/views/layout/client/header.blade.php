<?php
use App\Models\Office;
use App\Models\Content;
// print_r($subdomain);exit;
$languageId = Session::get('language_id');
$divisionOffices = [];

$officeData = Office::getOfficeDetailFromSubdomain($subdomain, $languageId);
$HeavydivisionOffices = Office::getOfficesByType($languageId, 4);
$MechanicalOffices = Office::getOfficesByType($languageId, 5);
$MainProjectOffices = Office::getOfficesByType($languageId, 6);

$todaysDateInGregorian = \Carbon\Carbon::now()->format('Y-m-d');
// dd($todaysDateInGregorian);
$nepalidate = Calendar::eng_to_nep_dor($todaysDateInGregorian);

?>
@if ($languageId == 2)
    <style type="text/css" media="screen">
        #cssmenu>ul>li>a {
            color: #ffffff;
            padding: 15px 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 9px;
            z-index: 2;
            position: relative;
        }

    </style>
@endif
<style type="text/css">
    .logotext {
        color: #134FB2;
        width: 80%;
        font-weight: bolder;
        float: right;
        display: inline-block;
        padding: 5px;
        line-height: 15px;
    }

    .logotext p {
        padding-bottom: 0px;
        margin-bottom: 0px;
        text-align: left;
    }

    #cse {
        width: 60%;
        /* make sure you don't use inline width */
        margin: 0;
        background-color: none !important;
    }

    .cse .gsc-search-button input.gsc-search-button-v2,
    input.gsc-search-button-v2 {
        background-color: none !important;
        background-color: #CECECE;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;

    }

    .cse .gsc-control-cse,
    .gsc-control-cse {
        background-image: linear-gradient(to top, #f0f0f0, #f4f4f4);
        border: 0px solid #FFF;
    }

</style>

<header id="header-full-top" class=" header-full">
    <div class="container">
        <div class="header-full-title">
            <h1 class="animated fadeInRight"><a href="{{ route('frontend.homepage', $subdomain) }}"
                    class="active">
                    <img src="{{ $abs_site_url }}assets/img/logo.png" alt=""></a></h1>
            <div class="logotext">
                <a href="{{ route('frontend.homepage', $subdomain) }}">
                    @if ($languageId == 1)
                        <p>नेपाल सरकार</p>
                        <small>पूर्वाधार विकास मन्त्रालय</small>
                        <p>सडक विभाग</p>
                    @else
                        <p>Government of Nepal</p>
                        <small>Ministry of Infrastructure Development</small>
                        <p>Department of Roads</p>
                    @endif
                    @if ($subdomain != 'home')
                        <small>{{ $officeData->office_name }}</small>
                    @endif
                </a>
            </div>
        </div>

        <nav class="top-nav hidden-xs hidden-md  hidden-sm">
            <ul class="top-nav-links-icons hidden-sm">

                <li>
                    <a href="{{ $abs_site_url . $subdomain }}"
                        class="animated fadeIn animation-delay-6 custom">{{ \Helper::getNameOftext('homepage', $languageId) }}</a>
                </li>
                <li>
                    <a href="{{ $abs_site_url . $subdomain . '/feedback' }}"
                        class="animated fadeIn animation-delay-7 custom">{{ \Helper::getNameOftext('feedback', $languageId) }}</a>
                </li>
                <li>
                    <a href="{{ $abs_site_url . $subdomain . '/laws' }}"
                        class="animated fadeIn animation-delay-8 custom">{{ \Helper::getNameOftext('laws', $languageId) }}</a>
                </li>
                <li>
                    <a href="{{ $abs_site_url . $subdomain . '/page/links' }}"
                        class="animated fadeIn animation-delay-9 custom">
                        {{ \Helper::getNameOftext('links', $languageId) }}</a>
                </li>
                <li>
                    <a href="{{ $abs_site_url . $subdomain . '/contact' }}"
                        class="animated fadeIn animation-delay-9 custom">{{ \Helper::getNameOftext('contact', $languageId) }}</a>
                </li>
                <li>
                    <a href="http://mail.dor.gov.np/" target="_blank"
                        class="animated fadeIn animation-delay-9 custom">{{ \Helper::getNameOftext('check-mail', $languageId) }}</a>
                </li>
                <?php if($languageId == 2){ ?>
                <li>
                    <div class="clock">
                        <div class="Date">{{ date('F d,Y') }} </div>
                        <ul>
                            <li id="hours"></li>
                            <li id="point">:</li>
                            <li id="min"></li>
                            <li id="point">:</li>
                            <li id="sec"></li>
                        </ul>
                    </div>
                </li>
                <?php }  else { ?>
                <li>
                    <div class="clock">
                        <div class="Date">{{ $nepalidate }} </div>
                        <ul id="current_time">
                            <li id="hours"></li>
                            <li id="point">:</li>
                            <li id="min"></li>
                            <li id="point">:</li>
                            <li id="sec"></li>
                        </ul>
                    </div>
                </li>
                <?php }  ?>
            </ul>


            <ul class="inline">
                <?php if($languageId == 1){ ?>
                <li class="language"><a href="javascript:void(0)" class="change_language"
                        id="language_2">ENGLISH</a></li>
                <li class="language">| नेपाली</li>
                <?php }  else { ?>
                <li class="language">ENGLISH | </li>
                <li class="language"><a href="javascript:void(0)" class="change_language"
                        id="language_1">नेपाली</a></li>
                <?php }  ?>
                <!-- 		<li></li> -->
                <li>
                    <form role="form" action="{{ $abs_site_url . $subdomain . '/search' }}" class="search"
                        method="get">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-ar btn-primary" type="submit">Go!</button>
                            </span>
                        </div><!-- /input-group -->
                    </form>
                </li>


            </ul>


        </nav>

        <div class="flag hidden-xs">

            <img class="logo-nep" src="{{ $abs_site_url }}assets/img/nepal_flag.gif" alt="">

        </div>

    </div> <!-- container -->


</header> <!-- header-full -->

<nav class="navbar navbar-static-top navbar-default navbar-header-full navbar-inverse" role="navigation" id="header">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#cssmenu">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand hidden-lg hidden-md hidden-sm active" href="{{ $abs_site_url . $subdomain }}"></a>
        </div> <!-- navbar-header -->

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="pull-right hidden-lg">
            <a href="javascript:void(0);" class="sb-icon-navbar sb-toggle-right"><i class="fa fa-bars"></i></a>
        </div>

        <div class=" navbar-collapse" id="cssmenu">
            <ul class="nav navbar-nav">

                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain }}">
                        {{ \Helper::getNameOftext('homepage', $languageId) }} </a>
                </li>
                <?php
				$headerContents = Content::getContentsPostionWise($languageId, $subdomain, 1, 0,5);
				// echo '<pre>';print_r($headerContents);exit;
				foreach($headerContents as $content){
					if($content->content_url == 'contact') continue;
					$subMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $content->id, 10);

					if(count($subMenus)>0){
						if($content->content_url == 'others') { ?>


                <?php
                $regionalHeading = $languageId == 1 ? 'क्षेत्रिय निर्देशानलय' : 'Regional Offices';

                ?>
                <li class="has-sub"><a href="#"><?php echo $regionalHeading; ?></a>
                    <?php
                    echo '<ul class="inside">';
                    $regionalOffices = Office::getActiveRegionalOffices($languageId, $subdomain);
                    foreach ($regionalOffices as $office) {
                        echo '<li><a href="' . $abs_site_url . $office->office_subdomain . '">' . $office->office_name . '</a></li>';
                    }

                    echo '</ul>';
                    ?>
                </li>
                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain . '/DRO' }}">
                        {{ \Helper::getNameOftext('division-road-offices', $languageId) }}</a>
                </li>
                <?php
          }
					echo '<li class="has-sub">';
					echo '<a href="'. $abs_site_url . $subdomain . '/page/' . $content->content_url .'">'. $content->content_heading .'</a>';
					echo '<ul class="inside">';
					foreach($subMenus as $subContent) {
						$childMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subContent->id, 10);
						if(count($childMenus)>0){
							echo '<li class="has-sub">';
							echo '<a href="'. $abs_site_url . $subdomain .'/page/'. $subContent->content_url .'">'. $subContent->content_heading.'</a>';
							echo '<ul class="inside">';
							foreach ($childMenus as $child) {
                // sub-child class start
                $subchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $child->id, 10);
                if(count($subchildMenus)>0){
                  echo '<li class="has-sub">';
                  echo '<a href="'. $abs_site_url . $subdomain .'/page/'. $child->content_url .'">'. $child->content_heading.'</a>';
                  echo '<ul class="inside">';
                  foreach ($subchildMenus as $subchild) {
                    // sub-child class start
                    $subsubchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subchild->id, 10);
                      if(count($subsubchildMenus)>0){
                        echo '<li class="has-sub">';
                        echo '<a href="'. $abs_site_url . $subdomain .'/page/'. $subchild->content_url .'">'. $subchild->content_heading.'</a>';
                        echo '<ul class="inside">';
                        foreach ($subsubchildMenus as $subsubchild) {
                          echo '<li class=""><a href="'. $abs_site_url . $subdomain .'/page/'. $subsubchild->content_url .'">'. $subsubchild->content_heading.'</a></li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                      } else {
                      echo '<li class=""><a href="'. $abs_site_url . $subdomain .'/page/'. $subchild->content_url .'">'. $subchild->content_heading.'</a></li>';
                    }
                    // end sub-child class
                    }
                    echo '</ul>';
                    echo '</li>';
                  } else {
                  echo '<li class=""><a href="'. $abs_site_url . $subdomain .'/page/'. $child->content_url .'">'. $child->content_heading.'</a></li>';
                }
                // end sub-child class
              }
							echo '</ul>';
							echo '</li>';
						} else {
						  echo '<li class=""><a href="'. $abs_site_url . $subdomain .'/page/'. $subContent->content_url .'">'. $subContent->content_heading.'</a></li>';
            }
          }

          if( $content->content_url == 'others') { ?>

                <li><a
                        href="{{ $abs_site_url . $subdomain . '/vehicles' }}">{{ \Helper::getNameOftext('vehicles', $languageId) }}</a>
                </li>
                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain . '/tenders' }}">
                        {{ \Helper::getNameOftext('tenders', $languageId) }}</a>
                </li>
                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain . '/news' }}">
                        {{ \Helper::getNameOftext('news', $languageId) }}</a>
                </li>
                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain . '/publications' }}">
                        {{ \Helper::getNameOftext('publications', $languageId) }}</a>
                </li>
                <li class="">
                    <a href=" {{ $abs_site_url . $subdomain . '/notices' }}">
                        {{ \Helper::getNameOftext('notices', $languageId) }}</a>
                </li>
                <?php

						}
						echo '</ul>';
						echo '</li>';
					} else {
						echo '<li class=""><a href="'. $abs_site_url . $subdomain . '/page/' . $content->content_url .'">'. $content->content_heading .'</a></li>';

					}
				}
				?>
                @if ($subdomain == 'home')
                    @if (count($HeavydivisionOffices) > 0)
                        <li class="has-sub">
                            <a href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions', $languageId) }}</a>
                            <ul>
                                <li class="has-sub">
                                    <a
                                        href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions', $languageId) }}</a>
                                    <ul>
                                        @foreach ($HeavydivisionOffices as $office)
                                            <li><a
                                                    href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="has-sub">
                                    <a href="#">{{ \Helper::getNameOftext('mechanical-offices', $languageId) }}</a>
                                    <ul>
                                        @foreach ($MechanicalOffices as $office)
                                            <li><a
                                                    href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </li>

                    @endif



                    <li class="has-sub">
                        <a href="#">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
                        <ul>

                            @if (count($MainProjectOffices) > 0)


                                @foreach ($MainProjectOffices as $office)
                                    <li>
                                        <a
                                            href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>

                                    </li>
                                @endforeach
                            @endif
                            <li class="">
                                <a href=" {{ $abs_site_url . $subdomain . '/projects' }}">
                                    {{ \Helper::getNameOftext('other-projects', $languageId) }}</a>
                            </li>


                        </ul>
                    </li>
                @else
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/home/about-us' }}">{{ \Helper::getNameOftext('about-us', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/home/working-area' }}">{{ \Helper::getNameOftext('working-area', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/home/dor-spoke-person' }}">{{ \Helper::getNameOftext('spokes-person', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/staffs' }}">{{ \Helper::getNameOftext('staffs', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/projects' }}">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/vehicles' }}">{{ \Helper::getNameOftext('vehicles', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/gallery' }}">{{ \Helper::getNameOftext('gallery', $languageId) }}</a>
                    </li>

                @endif
            </ul>
            @if ($subdomain != 'home')
                <a href="{{ $abs_site_url . 'home' }}"
                    class="butlike"><span>{{ \Helper::getNameOftext('switch-to-head-office', $languageId) }}</span></a>
            @endif
        </div>
        <!-- navbar-collapse -->
    </div><!-- container -->
</nav>
