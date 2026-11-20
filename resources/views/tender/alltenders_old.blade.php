@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'बोलपत्र/दरभाउपत्र ': 'Tender Notice';
	$title	= ($languageId == 1) ? 'शिर्षक': 'Title';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$notAvailableMessage	= ($languageId == 1) ? 'बोलपत्र/दरभाउपत्र उपलब्ध छैन ।': 'Tender Notices are not available.';
	$from = ($languageId == 1) ? 'म्याद समाप्त मिति देखी' : 'Expiry date from';
	$to = ($languageId == 1) ? 'सम्म' : 'to';
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
                    <h3 class="hentry__title">{{ $heading }}</h3>

					<?php if(count($tenders) > 0){ ?>

					<form href="" name="searchForm"  id="searchForm" method="post" >
						{{ $from }} <input name="expiry_from" class="date-picker" />
						{{ $to }} <input name="expiry_to" class="date-picker" />
						<input type="submit" value="submit" name="submit">
					</form>

                    <table class="roadproj table-striped table-condensed" id="normal_table">
							@if($languageId == 1)
								<th>क्र.स</th>
								<th>शिर्षक</th>
								<th>प्रकाशित मिति </th>
								<th>म्याद समाप्त मिति </th>
								<th>कार्यालय को नाम</th>
								<th>डाउनलोड गर्नुहोस्</th>
							@else
								<th>S N</th>
								<th>Title</th>
								<th>Published Date</th>
								<th>Expiry Date</th>
								<th>Office Name</th>
								<th>Download</th>
							@endif

								<?php
									$count = 1;
									foreach($tenders as $tender){
										$class = ($tender->expire_status == 0) ? 'active' : 'expired';
										echo '<tr class="'. $class .'">';
										echo '<td>'.$count++.'</td>';
										echo '<td><a href="tender/'. $tender->tender_url .'">'.$tender->tender_heading.'</td>';

										if($languageId == 1){
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'.\Calendar::eng_to_nep_dor($tender->published_date).'</td>';
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'.\Calendar::eng_to_nep_dor($tender->expiry_date).'</td>';
										} else {
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $tender->en_published_date .'</td>';
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $tender->en_expiry_date .'</td>';
										}

										echo '<td>'.$tender->office_name.'</td>';
										echo '<td>';
										if(file_exists("uploads/tender/".$tender->tender_attachment) && $tender->tender_attachment!='') {
											echo '<a href="'. $abs_site_url. $subdomain .'/tender/force/'. $tender->tender_url .'" target="_blank"><i class="glyphicon glyphicon-download mr6"></i>'. $download;
										} else {
											echo $fileNotfound;
										}
										echo '</td>';
										echo '</tr>';
									}
								?>
							</table>
							<?php  echo $tenders->links()  ?>
					<?php } else { ?>
							<p>{{ $notAvailableMessage }}</p>
					<?php } ?>

                 </div>
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
