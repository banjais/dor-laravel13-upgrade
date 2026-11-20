@extends('layout.client.container')
@section('content')
<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? $zoneName .'मा रहेका कार्यालयहरु ': 'Offices in '.$zoneName;
  $lowerHeading = ($languageId == 1) ? 'क्षेत्रीय कार्यालयहरु': 'Regional Offices';
  $lowerHeading2 = ($languageId == 1) ? 'हेअवी एकुइप्मेन्त डिभिजनस': 'Heavy Equipment Divisions';
	$lowerHeading3	= ($languageId == 1) ? 'मेशिनसम्बन्धी कार्यालयहरु': 'Mechanical offices';
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
					
                    <?php if(count($offices) > 0){ ?>
                    <ul class="mainlisting">
						<li class="list title"></li>
						<?php $count = 1 ?>
							@foreach($offices as $office)
								<li class="list">
                                    <ul class="ullist">
                                        <li class="sno"> 
                                            <span>{{ $count++ }}</span>
                                        </li>                                         
                                        <li class="grey">
											<a href="{{ $abs_site_url . $office->office_subdomain }}">{{ $office->office_name }}</a>
										</li>
                                    </ul>
                                </li>								
							@endforeach
					</ul>
					<?php } else { ?>									
							<div>{{ $notAvailableMessage }}</div>
					<?php } ?>
					<!-- 
                            <h3 class="">{{ $lowerHeading }}</h3>
                    
                              <?php 
                      $regionalOffices = Office::getActiveRegionalOffices($languageId, $subdomain);
                      if(count($regionalOffices) > 0){ 
                    ?>
                              <ul class="mainlisting">
                      <li class="list title"></li>
                      <?php $count = 1 ?>
                        @foreach($regionalOffices as $office)
                          <li class="list">
                                              <ul class="ullist">
                                                  <li class="sno"> 
                                                      <span>
                                {{ $count++ }}
                                                      </span>
                                                  </li>                                         
                                                  <li class="grey"><a href="{{ $abs_site_url . $office->office_subdomain }}"> {{ $office->office_name }}</a></li>
                                              </ul>
                                          </li>               
                        @endforeach
                    </ul>
                    <?php } else { ?>                 
                        <div>{{ $notAvailableMessage }}</div>
                    <?php } ?>  -->

             <h3 class="">{{ $lowerHeading2 }}</h3>
          
                    <?php 
            $regionalOffices = Office::getActiveHEDivisionsOffices($languageId, $zoneId,  $subdomain);
            if(count($regionalOffices) > 0){ 
          ?>
                    <ul class="mainlisting">
            <li class="list title"></li>
            <?php $count = 1 ?>
              @foreach($regionalOffices as $office)
                <li class="list">
                                    <ul class="ullist">
                                        <li class="sno"> 
                                            <span>
                      {{ $count++ }}
                                            </span>
                                        </li>                                         
                                        <li class="grey"><a href="{{ $abs_site_url . $office->office_subdomain }}"> {{ $office->office_name }}</a></li>
                                    </ul>
                                </li>               
              @endforeach
          </ul>
          <?php } else { ?>                 
              <div>{{ $notAvailableMessage }}</div>
          <?php } ?>
            <h3 class="">{{ $lowerHeading3 }}</h3>
					
                    <?php 
						$regionalOffices = Office::getActiveMechanicalOffices($languageId, $zoneId, $subdomain);
						if(count($regionalOffices) > 0){ 
					?>
                    <ul class="mainlisting">
						<li class="list title"></li>
						<?php $count = 1 ?>
							@foreach($regionalOffices as $office)
								<li class="list">
                                    <ul class="ullist">
                                        <li class="sno"> 
                                            <span>
											{{ $count++ }}
                                            </span>
                                        </li>                                         
                                        <li class="grey"><a href="{{ $abs_site_url . $office->office_subdomain }}"> {{ $office->office_name }}</a></li>
                                    </ul>
                                </li>								
							@endforeach
					</ul>
					<?php } else { ?>									
							<div>{{ $notAvailableMessage }}</div>
					<?php } ?>
					
                </div>
				 @include('layout.client.sidebar')
            </div>
            <hr class="dotted">
            
        </section>
</div>


@stop