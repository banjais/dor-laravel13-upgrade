@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
?>
    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ \Helper::getNameOftext('trainings', $languageId) }}</li>
				</ul>
			</div>
			<div class="row">
                 <div class="col-md-12 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
					<h3 class="hentry__title">{{ \Helper::getNameOftext('trainings', $languageId) }}</h3>
                    <div class="row">
                    <div class="col-lg-12">
					
					<?php if(count($trainings) > 0){ ?>
					
						<table class="roadproj table-striped table-condensed" id="training_table">
							
								<th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('title', $languageId) }} </th>
								<th>{{ \Helper::getNameOftext('start_date', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('end_date', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('contact_person', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('contact_number', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('number_of_trainees', $languageId) }}</th>
								<th></th>
								
								<?php 
									$count = 1;
									foreach($trainings as $training){
										echo '<tr>';
										echo '<td>'. $count++ .'</td>';
										echo '<td><a href="training/'. $training->training_url .'">'. $training->training_heading .'</a></td>';
										
										if($languageId == 1){
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. \Calendar::eng_to_nep_dor($training->training_start_date).'</td>';
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. \Calendar::eng_to_nep_dor($training->training_end_date).'</td>';
										} else {
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $training->en_training_start_date .'</td>';
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $training->en_training_end_date .'</td>';
										}
										echo '<td>'. $training->training_contact_person .'</td>';
										echo '<td>'. $training->training_contact_number .'</td>';
										echo '<td class="number_of_traniees">'. $training->number_of_traniees .'</td>';
										echo '<td><a href="training/'. $training->training_url .'">'. \Helper::getNameOftext('read-more', $languageId) .'</a></td>';
										echo '</tr>';
									}
								?>
							</table>
						{{ $trainings->links() }}
					<?php } else { ?>
							<p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
					<?php } ?>
					
				</div>
                </div>
                 </div>
            </div>
            <hr class="dotted">
            
        </section>
</div>
@stop