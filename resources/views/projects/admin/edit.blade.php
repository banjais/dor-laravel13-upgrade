@extends('layout.admin.masters')
@section('content')


        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Projects
							<small>Edit project.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.projects') }}">Project Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Project<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.project.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Project</button></a>
                            @endif
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Edit Project Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                            @include('layout.admin.alert')
                        </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($projects as $project)
                           <div class="control-group">
                           <label class="control-label">Project Heading ( {{ $project->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="project_heading_{{ $project->language_id }}" data-required="1" class="span8"
									value="{{ $project->project_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Project Url</label>
                            <div class="controls">
                                 <input type="text" name="project_url" id="project_url" data-required="1" class="span8"
								 value="{{ $projectData->project_url }}" />
                             </div>
                           </div>

						@foreach($projects as $project)
                           <div class="control-group">
                           <label class="control-label">Project Description ( {{ $project->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="project_description_{{ $project->language_id }}">
                                   {{{ $project->project_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($projects as $project)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $project->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="project_page_title_{{ $project->language_id }}" data-required="1" class="span8"
								 value=" {{ $project->project_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="project_meta_tags" >{{ $projectData->project_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="project_meta_description" id="project_meta_description">{{ $projectData->project_meta_description }}</textarea>
                            </div>
                        </div>

						<div class="control-group">
							<div class="span6">
							   <label class="control-label">Project Class : </label>
								<div class="controls">
									 <input type="text" name="project_class" id="project_class" data-required="1" class="gllpLatitude" value="{{ $projectData->project_class }}" />
								 </div>
							</div>
							<div class="span6">
								<label class="control-label">Project Ref No: </label>
								<div class="controls">
									<input type="text" name="project_ref_no" id="project_ref_no" data-required="1" class="gllpLongitude" value="{{ $projectData->project_ref_no }}" />
								</div>
							</div>
						</div>

						<div class="control-group">
							<div class="span6">
							   <label class="control-label">Project Link Code : </label>
								<div class="controls">
									 <input type="text" name="project_link_code" id="project_link_code" data-required="1" class="gllpLatitude" value="{{ $projectData->project_link_code }}" />
								 </div>
							</div>
							<div class="span6">
								<label class="control-label">Project BT: </label>
								<div class="controls">
									<input type="text" name="project_black_tapped" id="project_black_tapped" data-required="1" class="gllpLongitude" value="{{ $projectData->project_black_tapped }}" />
								</div>
							</div>
						</div>

						<div class="control-group">
							<div class="span6">
							   <label class="control-label">Project GR : </label>
								<div class="controls">
									 <input type="text" name="project_gravel" id="project_gravel" data-required="1" class="gllpLatitude" value="{{ $projectData->project_gravel }}" />
								 </div>
							</div>
							<div class="span6">
								<label class="control-label">Project ER: </label>
								<div class="controls">
									<input type="text" name="project_earthen" id="project_earthen" data-required="1" class="gllpLongitude" value="{{ $projectData->project_earthen }}" />
								</div>
							</div>
						</div>
						<div class="control-group">
							<div class="span6">
							   <label class="control-label">Project UC : </label>
								<div class="controls">
									 <input type="text" name="project_under_construction" id="project_under_construction" data-required="1" class="gllpLatitude" value="{{ $projectData->project_under_construction }}" />
								 </div>
							</div>
							<div class="span6">
								<label class="control-label">Project PL: </label>
								<div class="controls">
									<input type="text" name="project_plan" id="project_plan" data-required="1" class="gllpLongitude" value="{{ $projectData->project_plan }}" />
								</div>
							</div>
						</div>

						{{-- <fieldset class="gllpLatlonPicker">
						<div class="control-group">
                           <label class="control-label">Search Map : </label>
                            <div class="controls">
                                 <input type="text" class="gllpSearchField">
									<input type="button" class="gllpSearchButton" value="search">
									<br/><br/>
									<div class="gllpMap">Google Maps</div>
								<br/>
                             </div>
                        </div>
						<div class="control-group">
							<div class="span6">
							   <label class="control-label">Latitude : </label>
								<div class="controls">
									 <input type="text" name="project_latitude" id="project_latitude" data-required="1" class="gllpLatitude" value="{{ $projectData->project_latitude }}" />
								 </div>
							</div>
							<div class="span6">
							   <label class="control-label">Longitude: </label>
								<div class="controls">
									 <input type="text" name="project_longitude" id="project_longitude" data-required="1" class="gllpLongitude" value="{{ $projectData->project_longitude }}" />
								 </div>
							</div>
						</div>
						<div class="control-group">
                           <label class="control-label">Zoom: </label>
                            <div class="controls">
                                 <input type="text" data-required="1" class="gllpZoom" name="project_zoom" value="{{ $projectData->project_zoom }}" />
								 <input type="button" class="gllpUpdateButton" value="update map">
                             </div>
                        </div>
					</fieldset> --}}

                    <div class="control-group">
                        <label class="control-label">Google map embed code (with iframe): </label>
                        <div class="controls">
                            <textarea style="width:100%" rows="5" type="text" name="google_map_embed" id="google_map_embed" data-required="1"
                                class="gllpZoom" value="7">{{ $projectData->google_map_embed }}</textarea>
                        </div>
                    </div>
						<br />

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($projectData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($projectData->is_active == '1' || $projectData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.projects') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $project->project_id}}}">

							@foreach($projects as $project)
								<input type="hidden" name="detail_id_{{ $project->language_id }}" value="{{{ $project->project_detail_id}}}">
							@endforeach
                        </form>

                     </div>
                  </div>

               </div>
            </div>
              </div>

			</div>

		</div>

@section('footer_js')
<script type="text/javascript">
	$('li').removeClass('active');
	$('#content').addClass('active');
	$('#projects').addClass('active');
<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'project_description_<?php echo $language->id ?>',{
			filebrowserBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html',
			filebrowserImageBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Images',
			filebrowserFlashBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Flash',
			filebrowserUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
			filebrowserImageUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
			filebrowserFlashUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
			filebrowserWindowWidth : '1000',
			filebrowserWindowHeight : '700'
		});
<?php } ?>

(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#editform").validate({
                rules: {
                    project_url: "required",
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    project_url: "Please enter url of the project/page. ",

					attachment		:	{
											extension	:	"Please select appropriate file. Only jpeg/jpg and png format are allowed."
										}

                },
			});

        }

    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);
</script>

// <!--<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> -->
// <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg&sensor=false"></script>

<!-- CSS and JS for our code -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/bootstrap-datepicker/css/datepicker.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.css') }}"/>
<script src="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.js') }}"></script>
@endsection

@stop
