@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'उपकरण': 'Equipments';
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
						<div class="row">
						<?php if(count($equipments) > 0) { ?>
							<table class="roadproj table-striped table-condensed" id="project_table">
							@if($languageId == 1)
								<th>क्र.स</th>
								<th>उपकरणको नाम</th>
								<th>उपकरणको पहिचान</th>
								<th> श्रेणी /  क्षमता</th>
								<th>डिओर  कोड </th>
								<th>प्रती घण्टा रु.</th>
								<th>प्रती  शिफ्ट रु.</th>
							@else
								<th>S N</th>
								<th>Equipment Name</th>
								<th>Equipment Identification</th>
								<th>Category/Capacity </th>
								<th>Dor code </th>
								<th>Per Hour Rs.</th>
								<th>Per Shift Rs.</th>
							@endif
								
								<?php 
									$count = 1;
									foreach($equipments as $equipment){
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td>'.$equipment->equipment_name.'</td>';
										echo '<td>'.$equipment->equipment_identification.'</td>';
										echo '<td>'.$equipment->equipment_capacity_category.'</td>';
										echo '<td>'.$equipment->equipment_dor_code.'</td>';
										echo '<td>'.$equipment->equipment_per_hour.'</td>';
										echo '<td>'.$equipment->equipment_per_shift.'</td>';
										echo '</tr>';
									}
								?>
							</table>							  
							<?php  echo $equipments->links();  ?>
							<?php } else { ?>
								<p>{{ $notAvailableMessage }}</p>
							<?php } ?>
						</div>
					</div>
                </div>
            </div>
            <hr class="dotted">
            
        </section>
</div>
@stop