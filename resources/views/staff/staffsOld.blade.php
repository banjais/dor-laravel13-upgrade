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
					<li>{{ \Helper::getNameOftext('staffs', $languageId) }}</li>
				</ul>
			</div>
			<div class="row">
                 <div class="col-md-12 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                   <h3 class="hentry__title">{{ \Helper::getNameOftext('staffs', $languageId) }}</h3>
				   
                    <div class="row">
                    
					@if($languageId == 1)
					@foreach($staffs->chunk(4) as $chunk)						
                        <div class="row">
                        @foreach ($chunk as $staff)
                    	<div class="col-md-3">     
                        <div class="stafflist">
                        @foreach($staff->details as $detail)
                        @endforeach                  
						@if(file_exists('uploads/staffs/'.$staff->attachment) && $staff->attachment != '')
							<img src="{{ asset('uploads/staffs/'.$staff->attachment) }}" alt="{{ $detail->full_name}}" class="img-responsive">
						@else
							<img src="{{ asset('assets/img/noimage.jpg') }}" alt="{{ $detail->full_name}}" class="img-responsive">
						@endif
						<br>
							<address>
								<p> <strong>{{ \Helper::getNameOftext('staff_name', $languageId) }} : </strong><span>{{ $detail->full_name }}</span></p>
								<p> <strong>{{ \Helper::getNameOftext('post_name', $languageId) }}: </strong><span>@if($staff->designation){{ $staff->designation->heading }}@endif</span></p>
								<p> <strong>{{ \Helper::getNameOftext('post_label', $languageId) }}: </strong><span>@if($staff->level){{ $staff->level->heading }}@endif</span></p>
								<p> <strong>{{ \Helper::getNameOftext('service', $languageId) }}:</strong><span>@if($staff->service){{ $staff->service->heading }}@endif</span></p>
								<p> <strong>{{ \Helper::getNameOftext('remarks', $languageId) }}:</strong><span>@if($staff->remarks){{ $staff->remarks }}@endif</span></p>
							</address>
                        </div>
                        </div>
                        @endforeach	
                        </div>
                        <div class="clearfix"></div>
					@endforeach					
					@endif 
					
                    </div>
                      
                 </div>
                 
            </div>
            <hr class="dotted">
            
        </section>
</div>



@stop