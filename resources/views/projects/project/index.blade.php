@extends('layout.project.masters')
@section('content')
@php
    $projectDetail = $project->detail_eng->first() ?: $project->detail_nep->first();
@endphp

		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Dashboard
							<small>Reports, alerts, notifications and more</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                Dashboard<span class="divider">&nbsp;</span>
                            </li>

                        </ul>

					</div>
			  </div>


              <div id="page">
              <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN widget widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>Project : {{ optional($projectDetail)->project_heading }}</h4>
                            <a href="{{ route('project.admin.edit', $project->id) }}"><button class="btn  btn-primary pull-right"><i class="icon-plus"></i> Edit Project</button></a>
                        </div>
                        <div class="widget-body">

                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Fields</th>
                                        <th>Values</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Project Name</td>
                                            <td>{{ optional($projectDetail)->project_heading }}</td>
                                        </tr>
                                        <tr>
                                            <td>Meta Tags</td>
                                            <td>{{ $project->project_meta_tags }}</td>
                                        </tr>
                                        <tr>
                                            <td>Meta Description</td>
                                            <td>{{ $project->project_meta_description }}</td>
                                        </tr>
                                        <tr>
                                            <td>Class</td>
                                            <td>{{ $project->project_class }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ref No</td>
                                            <td>{{ $project->project_ref_no }}</td>
                                        </tr>
                                        <tr>
                                            <td>Link Code</td>
                                            <td>{{ $project->project_link_code }}</td>
                                        </tr>
                                        <tr>
                                            <td>Black Tapped</td>
                                            <td>{{ $project->project_black_tapped }}</td>
                                        </tr>
                                        <tr>
                                            <td>Gravel</td>
                                            <td>{{ $project->project_gravel }}</td>
                                        </tr>
                                        <tr>
                                            <td>Earthen</td>
                                            <td>{{ $project->project_earthen }}</td>
                                        </tr>
                                        <tr>
                                            <td>Under Construction</td>
                                            <td>{{ $project->project_under_construction }}</td>
                                        </tr>
                                        <tr>
                                            <td>Plan</td>
                                            <td>{{ $project->project_plan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Location</td>
                                            <td>{{ optional($projectDetail)->project_location }}</td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td>{{ optional($projectDetail)->project_description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END widget widget-->
                </div>
            </div>
              </div>

			</div>

		</div>
	@stop
