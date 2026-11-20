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
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                   <h3 class="hentry__title">{{ \Helper::getNameOftext('staffs', $languageId) }}</h3>
				   
                    <div class="row">
                    <div class="col-md-12">
                    	<div class="table-responsive">
                    		
                    <table id="normal_table" class="table table-bordered">                     
                       <tr>
                        <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('post_name', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('post_label', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('service', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('group', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('sub_group', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('staff_name', $languageId) }}</th>
						<th>{{ \Helper::getNameOftext('contact_number', $languageId) }}</th>
						<th>{{ \Helper::getNameOftext('contact_email', $languageId) }}</th>
                        <th>{{ \Helper::getNameOftext('remarks', $languageId) }}</th>
                      </tr>                     
                      @foreach($staffs as $index=>$staff)
                        @foreach($staff->details as $detail)
                        @endforeach
                       <tr>
                        <td>{{ $index+1 }}</td>
                        <td>@if($staff->designation){{ $staff->designation->heading }}@endif</td>
                        <td>@if($staff->level){{ $staff->level->heading }}@endif</td>
                        <td>@if($staff->service){{ $staff->service->heading }}@endif</td>
                        <td>@if($staff->group){{ $staff->group->heading }}@endif</td>
                        <td>@if($staff->subgroup){{ $staff->subgroup->heading }}@endif</td>
                        <td>{{ $detail->full_name }}</td>
						  <td>{{ $detail->contact_number }}</td>
						    <td>{{ $staff->email_address }}</td>
                        <td>{{ $detail->remarks }}</td>
                       </tr>                      
                      @endforeach                      
                    </table>
                    
                    	</div>
                    </div>
					<!-- @if($languageId == 1)
					@foreach($staffs as $staff)
                    	<div class="col-md-4 ">     
                        <div class="stafflist">                   
						<?php 
								//if(file_exists( 'uploads/staffs/'.$staff->staff_attachment) && $staff->staff_attachment != '') {
							?>
								<img src="{{ $abs_site_url }}uploads/staffs/{{ $staff->staff_attachment }}" alt="{{ $staff->staff_full_name}}" class="img-responsive">
							<?php //  else { ?>
								<img src="{{ $abs_site_url }}assets/img/noimage.jpg"alt="{{ $staff->staff_full_name}}" class="img-responsive">
							<?php //} ?>
						
							   <br>
							<address>
								<p> <strong>नाम : </strong><span>{{ $staff->staff_full_name }}</span></p>
								<p> <strong>पद : </strong><span>{{ $staff->staff_designation }}</span></p>
								<p> <strong>ईमेल : </strong><span><a href="mailto:{{ $staff->staff_email_address }}">{{ $staff->staff_email_address }}</a></span></p>
								<p> <strong>फोन :</strong> <span><a href="tel:{{ $staff->staff_contact_number }}">{{ $staff->staff_contact_number }}</a></span></p>
							</address>
                        </div>
                        </div>
					@endforeach
					
					@else 
						
					@foreach($staffs as $staff)
						<div class="col-md-4">
							<?php 
								//if(file_exists( 'uploads/staffs/'.$staff->staff_attachment) && $staff->staff_attachment != '') {
							?><div class="staffimg">
								<img src="{{ $abs_site_url }}uploads/staffs/{{ $staff->staff_attachment }}" alt="{{ $staff->staff_full_name}}" class="img-responsive"> </div>
							<?php // }  else { ?>
								<div class="staffimg"><img src="{{ $abs_site_url }}assets/img/noimage.jpg"alt="{{ $staff->staff_full_name}}" class="img-responsive"> </div>
							<?php // } ?>
							 
							<address>
								<p> <strong>Name :</strong> <span>{{ $staff->staff_full_name }}</span> </p>
								<p> <strong>Position :</strong> <span>{{ $staff->staff_designation }}</span> </p>
								<p> <strong>E-Mail :</strong> <span><a href="mailto:{{ $staff->staff_email_address }}">{{ $staff->staff_email_address }}</a></span></p>
								<p> <strong>Tel :</strong> <span><a href="tel:{{ $staff->staff_contact_number }}">{{ $staff->staff_contact_number }}</a></span></p>
							</address>
                        </div>
						@endforeach
					@endif -->
					
                    </div>
                      
                 </div>
                 @include('layout.client.sidebar')
            </div>
            <hr class="dotted">
            
        </section>
</div>



@stop