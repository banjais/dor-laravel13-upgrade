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
					<li><a href="{{ $abs_site_url . $subdomain .'/trainings' }}">{{ \Helper::getNameOftext('trainings', $languageId) }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $trainingData->training_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title"> {{ $trainingData->training_heading }}</h3>
					<p> <strong>{{ \Helper::getNameOftext('start_date', $languageId) }} : </strong> <span><?php echo \Calendar::eng_to_nep_dor($trainingData->training_start_date) ?></span> </p>
					<p> <strong>{{ \Helper::getNameOftext('end_date', $languageId) }} : </strong> <span><?php echo \Calendar::eng_to_nep_dor($trainingData->training_end_date) ?></span> </p>
					<p> <strong>{{ \Helper::getNameOftext('contact_person', $languageId) }} : </strong> <span>{{ $trainingData->training_contact_person }}</span> </p>
					<p> <strong>{{ \Helper::getNameOftext('contact_number', $languageId) }} : </strong> <span>{{ $trainingData->training_contact_number }}</span> </p>
					<p> <strong>{{ \Helper::getNameOftext('number_of_trainees', $languageId) }} : </strong> <span id="number_of_traniees">{{ $trainingData->number_of_traniees }}</span> </p>
					<p> <strong>{{ \Helper::getNameOftext('minimum_qualification', $languageId) }}  : </strong> <span>{{ $trainingData->minimum_qualification }}</span> </p>
					
					<div>{{ $trainingData->training_description }}</div>
					@if($trainingData->training_remarks != '')
					<p> <strong>{{ \Helper::getNameOftext('remarks', $languageId) }} : </strong> <span></span> </p>
					<div>{{ $trainingData->training_remarks }}</div>
					@endif
                 </div>
               @include('layout.client.sidebar')
            </div>            
        </section>
	</div>


@stop