@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading = ($languageId == 1) ? 'सवारी साधन ': 'Vehicles';
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
                 <div class="col-md-12 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
					<h3 class="hentry__title"><strong>{{ $heading }}</strong></h3>
                    <div class="col-lg-12">
					
					<?php if(count($vehicles) > 0){ ?>
					
						<table class="roadproj table-striped table-condensed" id="vehicle_table">
							@if($languageId == 1)
								<th>क्र.स</th>
								<th>विवरण</th>
								<th>Vehicle number</th>
								<th>अवस्था</th>
								<th>Status </th>
							
							@else
								<th>S N</th>
								<th>Type</th>
								<th>Vehicle number</th>
								<th>Condition</th>
								<th>Condition Type</th>
								
							@endif
								
								<?php 
									$count = 1;
									foreach($vehicles as $vehicle){
									
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td>'.$vehicle->vehicle_type.'</td>';
										echo '<td>'.$vehicle->vehicle_number.'</td>';
										echo '<td>'.$vehicle->condition.'</td>';
										echo '<td>'.(($vehicle->condition_type == '1')?'Running':'Stopped').'</td>';
										echo '</tr>';
									}
								?>
							</table>
							<?php  echo $vehicles->links()  ?>
					<?php } else { ?>
							<p>{{ $notAvailableMessage }}</p>
					<?php } ?>
					
				</div>
                 </div>
            </div>
            <hr class="dotted">
            
        </section>
</div>
@stop