@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'डाउनलोड': 'Downloads';
	$sn	= ($languageId == 1) ? 'क्र.स': 'S N';
	$title	= ($languageId == 1) ? 'शिर्षक': 'Title';
	$publishDate	= ($languageId == 1) ? 'प्रकाशित मिति': 'Published Date';
	$downloadHere	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';

	$fileNotfound	= ($languageId == 1) ? 'फाइल उपलब्ध छैन': 'File Not found.';
	$notAvailableMessage	= ($languageId == 1) ? 'कुनैपनि रेकर्ड पाइएन ।': 'Records are Not Available.';
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

					<?php if(count($downloads) > 0) { ?>
							<table class="roadproj table-striped table-condensed" id="normal_table">
								<th>{{ $sn }}</th>
								<th>{{ $title }}</th>
								<th>{{ $publishDate }}</th>
								<th>{{ $downloadHere }}</th>
								<?php
									$count = 1;
									foreach($downloads as $download){
										$publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($download->published_date) : $download->en_published_date ;

										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td>'.$download->download_heading.'</td>';
										echo '<td><i class="glyphicon glyphicon-calendar mr5"></i>'.$publishedDate.'</td>';
										echo '<td>';
										if(file_exists("uploads/download/".$download->download_attachment) && $download->download_attachment!=''){
										echo '<span><a href="'. $abs_site_url.$subdomain .'/download/force/'.$download->download_url .'" target="_blank">
											<i class="glyphicon glyphicon-download mr6"></i>'. $downloadHere .'</a>
										</span>';
										} else { echo $fileNotfound; }
										echo '</td>';


										echo '</tr>';
									}
								?>
							</table>

						<?php  echo $downloads->links()  ?>
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
