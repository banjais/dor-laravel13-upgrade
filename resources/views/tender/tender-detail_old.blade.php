@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$tenderheading	= ($languageId == 1) ? 'बोलपत्र/दरभाउपत्र ': 'Tender Notice';
	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$publishedDate = ($languageId == 1) ? 'प्रकाशित मिति : ': 'Published Date : ';
	$expiryDate = ($languageId == 1) ? 'अन्तिम मिति : ': 'Expiry Date : ';

?>
	<div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li><a href="{{ $abs_site_url . $subdomain .'/tenders' }}">{{ $tenderheading }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ $tenderData->tender_heading }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-8 wow fadeInRight animated listdetail" style="visibility: visible; -webkit-animation: fadeInRight;">
                     <h3 class="hentry__title">{{ $tenderData->tender_heading }}</h3>

                    <div class="col-lg-12">
                    	<?php if($languageId == 1){ ?>
	                    	<p>{{ $publishedDate . \Calendar::eng_to_nep_dor($tenderData->published_date) }}</p>
	                    	<p>{{ $expiryDate . \Calendar::eng_to_nep_dor($tenderData->expiry_date) }}</p>
                    	<?php } else { ?>
                    		<p>{{ $publishedDate . $tenderData->en_published_date }}</p>
	                    	<p>{{ $expiryDate . $tenderData->en_expiry_date }}</p>
                    	<?php } ?>

                    	<div>{{ $tenderData->tender_description }}</div>

                        <br>
						<?php
						if(file_exists("uploads/tender/".$tenderData->tender_attachment) && $tenderData->tender_attachment!=''){
						?>
							<p>{{ $download }}
								<a href="{{ $abs_site_url. $subdomain }}/tender/force/{{ $tenderData->tender_url }}" target="_blank"><i class="glyphicon glyphicon-download mr6"></i></a>
							</p>

						<?php } ?>
                    </div>


                 </div>
               @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
