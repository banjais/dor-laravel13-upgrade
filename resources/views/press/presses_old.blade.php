@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'प्रेस विज्ञप्ति': 'Press Release';
	$sn	= ($languageId == 1) ? 'क्र.स': 'S N';
	$title	= ($languageId == 1) ? 'शिर्षक': 'Title';
	$publishDate	= ($languageId == 1) ? 'प्रकाशित मिति': 'Published Date';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';

	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$notAvailableMessage	= ($languageId == 1) ? 'प्रेस विज्ञप्ति उपलब्ध छैन ।': 'Press release are not available.';

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

					<?php if(count($presses) > 0) { ?>
							<table class="roadproj table-striped table-condensed" id="normal_table">
								<th>{{ $sn }}</th>
								<th>{{ $title }}</th>
								<th>{{ $publishDate }}</th>
								<th>{{ $download }}</th>

								<?php
									$count = 1;
									foreach($presses as $press){
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td><a href="press/'. $press->press_url .'">'.$press->press_heading.'</a></td>';
										if($languageId == 1){
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'.\Calendar::eng_to_nep_dor($press->published_date).'</td>';
										} else {
											echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'. $press->en_published_date .'</td>';
										}

										echo '<td>';
										if(file_exists("uploads/press/".$press->press_attachment) && $press->press_attachment!=''){
										echo '<span><a href="'. $abs_site_url.$subdomain .'/press/force/'.$press->press_url .'" target="_blank">
											<i class="glyphicon glyphicon-download mr6"></i>'. $download .'</a>
										</span>';
										} else { echo $fileNotfound; }
										echo '</td>';


										echo '</tr>';
									}
								?>
							</table>

						<?php  echo $presses->links()  ?>
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
