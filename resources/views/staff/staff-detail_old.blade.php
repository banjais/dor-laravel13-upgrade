@extends('layout.client.container')
@section('content')

<?php 
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'कर्मचारी को विवरण': 'Staff Profile';
?>

	 <div class="container">
        <section class="margin-bottom">
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="">{{ $heading }}</h3>
					
                    <div class="col-md-12">
                    	<div class="col-md-4">
                        <?php 
							if(file_exists( 'uploads/staffs/'.$staffData->staff_attachment) && $staffData->staff_attachment != '') {
						?>
							<img src="{{ $abs_site_url }}uploads/staffs/{{ $staffData->staff_attachment }}" alt="{{ $staffData->staff_full_name}}" class="img-responsive">
						<?php } else { ?>
							<img src="{{ $abs_site_url }}assets/img/noimage.jpg"alt="{{ $staffData->staff_full_name}}" class="img-responsive">
						<?php } ?>
                       <br>
						@if($languageId == 1)
							<address>
								<p> <strong>नाम : </strong> <span>{{ $staffData->staff_full_name }}</span> </p>
								<p> <strong>पद : </strong> <span>{{ $staffData->staff_designation }}</span> </p>
								<p> <strong>ईमेल : </strong> <span><a href="mailto:{{ $staffData->staff_email_address }}">{{ $staffData->staff_email_address }}</a></span> </p>
								<p> <strong>फोन :</strong> <span><a href="tel:+17079217269">{{ $staffData->staff_contact_number }}</a></span> </p>
							</address>
						@else 
							<address>
								<p> <strong>Name :</strong> <span>{{ $staffData->staff_full_name }}</span> </p>
								<p> <strong>Position :</strong> <span>{{ $staffData->staff_designation }}</span> </p>
								<p> <strong>E-Mail :</strong> <span><a href="mailto:{{ $staffData->staff_email_address }}">{{ $staffData->staff_email_address }}</a></span> </p>
								<p> <strong>Tel :</strong> <span><a href="tel:+17079217269">+1 707 921 7269</a></span> </p>
							</address>
						@endif
                       
						
                        </div>
                    	<div class="col-md-8">
                        <h4 class="">About Staff </h4>
                        <p><span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nobis, delectus, ea praesentium dicta aliquid deserunt debitis eos animi quibusdam alias sequi voluptatem enim asperiores quisquam sunt aliquam sint quis iste!</span>
                        <span>Enim, nesciunt, rerum, obcaecati blanditiis repudiandae iusto fuga eius natus quod eaque cumque amet repellendus expedita sunt in suscipit explicabo accusantium esse laborum ea. Numquam, ipsa blanditiis ea dolores quo.</span>
                        <span>Sapiente, deleniti ducimus magni rerum esse qui soluta numquam molestiae dolor deserunt nostrum alias veritatis similique ab harum natus doloremque eos ea itaque expedita saepe at earum totam recusandae dignissimos!</span></p>
                        </div>
                    </div>
                      
                 </div>
                @include('layout.client.mapview')
            </div>
            <hr class="dotted">
            
        </section>
</div>



@stop