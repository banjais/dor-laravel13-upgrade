@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'नियम कानुन': 'Laws';
	$sn	= ($languageId == 1) ? 'क्र.स': 'S N';
	$title	= ($languageId == 1) ? 'शिर्षक': 'Title';
	$publishDate	= ($languageId == 1) ? 'प्रकाशित मिति': 'Published Date';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$notAvailableMessage	= ($languageId == 1) ? 'नियम कानुन उपलब्ध छैन ।': 'Laws are not available.';

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
                    <h3 class="hentry__title">{{ $heading }} </h3>

					<?php if(count($laws) > 0) { ?>
							<table class="roadproj table-striped table-condensed" id="normal_table">
								<th>{{ $sn }}</th>
								<th>{{ $title }}</th>
								<th>{{ $publishDate }}</th>
								<th>{{ $download }}</th>

								<?php
									$count = 1;
									foreach($laws as $law){
										$lawDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($law->published_date) : $law->en_published_date;
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td><a href="law/'. $law->law_url .'">'.$law->law_heading.'</a></td>';
										echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $lawDate .'</td>';
										echo '<td>';
										if(file_exists("uploads/law/".$law->law_attachment) && $law->law_attachment!=''){
										echo '<span><a href="'. $abs_site_url.$subdomain .'/law/force/'.$law->law_url .'" target="_blank">
											<i class="glyphicon glyphicon-download mr6"></i>'. $download .'</a>
										</span>';
										} else { echo $fileNotfound; }
										echo '</td>';


										echo '</tr>';
									}
								?>
							</table>

						<?php  echo $laws->links()  ?>
						<?php } else { ?>
							<div>{{ $notAvailableMessage }}</div>
						<?php } ?>
                 </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
