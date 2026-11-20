@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
  $heading  = ($languageId == 1) ? 'कर्मचारीहरु': 'Staff Lists';
	$heading	= ($languageId == 1) ? 'कर्मचारीहरु': 'Staff Lists';
  $sn = 1;
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
				   
                    <div class="row">
                    <table class="table table-bordered">
                     @if($languageId == 1)
                      <tr>
                        <th>सि.न</th>
                        <th>पदको नाम</th>
                        <th>श्रेणी तह</th>
                        <th>सेवा</th>
                        <th>समुह</th>
                        <th>उप समुह</th>
                        <th>साविक पद</th>
                        <th>कर्मचारीको नाम</th>
                        <th>कैफियत </th>
                      </tr>
                      @else
                       <tr>
                        <th>S.N</th>
                        <th>Post Name</th>
                        <th>Post Label</th>
                        <th>Service</th>
                        <th>Group</th>
                        <th>Sub Group</th>
                        <th>Label</th>
                        <th>Staff Name</th>
                        <th>Remarks</th>
                      </tr>
                      @endif
                     
                      @if($languageId == 1)
          @foreach($staffs as $staff)

           <tr>
                        <td>{{ $sn++}}</td>
                        <td>{{ $staff->user_designation }}</td>
                        <td>{{ $staff->user_label }}</td>
                        <td>{{ $staff->user_service }}</td>
                        <td>{{ $staff->user_group }}</td>
                        <td>{{ $staff->user_subgroup }}</td>
                        <td>{{ $staff->user_staff_id }}</td>
                        <td>{{ $staff->user_full_name }}</td>
                        <td>{{ $staff->user_remark }}</td>
                      </tr>
                      
          @endforeach
          
          @else 
            
          @foreach($staffs as $staff)
          <tr>
                        <td>{{ $sn++ }}</td>
                        <td>{{ $staff->user_designation }}</td>
                        <td>{{ $staff->user_label }}</td>
                        <td>{{ $staff->user_service }}</td>
                        <td>{{ $staff->user_group }}</td>
                        <td>{{ $staff->user_subgroup }}</td>
                        <td>{{ $staff->user_staff_id }}</td>
                        <td>{{ $staff->user_full_name }}</td>
                        <td>{{ $staff->user_remark }}</td>
                      </tr>
                      
            @endforeach
          @endif
                      
                      
                    </table>
					<!-- @if($languageId == 1)
					@foreach($staffs as $staff)
                    	<div class="col-md-4 ">     
                        <div class="stafflist">                   
						<?php 
								//if(file_exists( 'uploads/users/'.$staff->user_attachment) && $staff->user_attachment != '') {
							?>
								<img src="{{ $abs_site_url }}uploads/users/{{ $staff->user_attachment }}" alt="{{ $staff->user_full_name}}" class="img-responsive">
							<?php //  else { ?>
								<img src="{{ $abs_site_url }}assets/img/noimage.jpg"alt="{{ $staff->user_full_name}}" class="img-responsive">
							<?php //} ?>
						
							   <br>
							<address>
								<p> <strong>नाम : </strong><span>{{ $staff->user_full_name }}</span></p>
								<p> <strong>पद : </strong><span>{{ $staff->user_designation }}</span></p>
								<p> <strong>ईमेल : </strong><span><a href="mailto:{{ $staff->user_email_address }}">{{ $staff->user_email_address }}</a></span></p>
								<p> <strong>फोन :</strong> <span><a href="tel:{{ $staff->user_contact_number }}">{{ $staff->user_contact_number }}</a></span></p>
							</address>
                        </div>
                        </div>
					@endforeach
					
					@else 
						
					@foreach($staffs as $staff)
						<div class="col-md-4">
							<?php 
								//if(file_exists( 'uploads/users/'.$staff->user_attachment) && $staff->user_attachment != '') {
							?><div class="staffimg">
								<img src="{{ $abs_site_url }}uploads/users/{{ $staff->user_attachment }}" alt="{{ $staff->user_full_name}}" class="img-responsive"> </div>
							<?php // }  else { ?>
								<div class="staffimg"><img src="{{ $abs_site_url }}assets/img/noimage.jpg"alt="{{ $staff->user_full_name}}" class="img-responsive"> </div>
							<?php // } ?>
							 
							<address>
								<p> <strong>Name :</strong> <span>{{ $staff->user_full_name }}</span> </p>
								<p> <strong>Position :</strong> <span>{{ $staff->user_designation }}</span> </p>
								<p> <strong>E-Mail :</strong> <span><a href="mailto:{{ $staff->user_email_address }}">{{ $staff->user_email_address }}</a></span></p>
								<p> <strong>Tel :</strong> <span><a href="tel:{{ $staff->user_contact_number }}">{{ $staff->user_contact_number }}</a></span></p>
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