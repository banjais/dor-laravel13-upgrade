@extends('layout.client.container')
@section('content')

<?php
	$languageId = Session::get('language_id');
?>
   <div class="container">
        <section class="margin-bottom">
			<div class="breadcrum">
				<ul>
					<li><a href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}</a></li>
					<li><i class="fa fa-chevron-right"></i></li>
					<li>{{ \Helper::getNameOftext('projects', $languageId) }}</li>
				</ul>
			</div>
            <div class="row">
                 <div class="col-md-12 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ \Helper::getNameOftext('projects', $languageId) }}</h3>
						<div class="">
						<?php if(count($projects) > 0) { ?>
							<table class="roadproj table-striped table-condensed" id="project_table">
								<th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('project_name', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('reference_number', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('link_code', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('bt', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('gr', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('er', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('total', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('uc', $languageId) }}</th>
								<th>{{ \Helper::getNameOftext('pl', $languageId) }}</th>
								<th></th>

								<?php
									$page = (!empty($_GET['page'])) ?  $_GET['page']: 1;
									$paginate = $projects->perPage();
									$count = ($page-1)*$paginate + 1;
									foreach($projects as $project){
										echo '<tr>';
										echo '<td>'.$count++.'</td>';
										echo '<td><a href="project/'. $project->project_url .'">'.$project->project_heading.'</a></td>';
										echo '<td>'.$project->project_class.'</td>';
										echo '<td>'.$project->project_ref_no.'</td>';
										echo '<td>'.$project->project_link_code.'</td>';
										echo '<td class="project_bt">'.$project->project_black_tapped.'</td>';
										echo '<td>'.$project->project_gravel.'</td>';
										echo '<td>'.$project->project_earthen.'</td>';
										echo '<td>'.round($project->project_total, 2).'</td>';
										echo '<td>'.$project->project_under_construction.'</td>';
										echo '<td>'.$project->project_plan.'</td>';
										echo '<td><a href="project/'. $project->project_url .'">'. \Helper::getNameOftext('read-more', $languageId) .'</a></td>';
										echo '</tr>';
									}
								?>
							</table>

						<?php  echo $projects->links()  ?>
						<?php } else { ?>
							<p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
						<?php } ?>
                    </div>
                 </div>
            </div>
            <hr class="dotted">

        </section>
</div>

@stop
