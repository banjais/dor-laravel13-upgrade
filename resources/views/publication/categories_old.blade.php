@extends('layout.client.container')
@section('content')

<?php
	$languageId = languageId();
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$heading	= ($languageId == 1) ? 'प्रकाशन': 'Publications';
	$sn	= ($languageId == 1) ? 'क्र.स': 'S N';
	$title	= ($languageId == 1) ? 'प्रकाशन वर्ग': 'Publication Category';
	$readmore	= ($languageId == 1) ? 'पुरा हेर्नुहोस्  ': 'View all';
	$notAvailableMessage	= ($languageId == 1) ? 'प्रकाशन उपलब्ध छैन ।': 'Publications are not available.';
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

					<?php if(count($categories) > 0) {
						$page = (!empty($_GET['page'])) ?  $_GET['page']: 1;
						$paginate = $categories->perPage();
						$count = ($page-1)*$paginate + 1;
					?>
							<table class="roadproj table-striped table-condensed" id="normal_table">
								<th>{{ $sn }}</th>
								<th>{{ $title }}</th>
								<th></th>
								<?php
									foreach($categories as $category){
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td><a href="publication/'. $category->category_url .'">'.$category->category_heading.'</a></td>';
										echo '<td><a href="publication/'. $category->category_url .'">'.$readmore.'</a></td>';
										echo '</tr>';
									}
								?>
							</table>

						<?php  echo $categories->links()  ?>
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
