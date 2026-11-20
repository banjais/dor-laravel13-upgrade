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
							Category
							<small>Add category.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.categories') }}">Category Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Add Category<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->


              </a>
              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Add Category Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">
@csrf
						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Category Heading ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="category_heading_{{ $language->id }}" id="category_heading_{{ $language->id }}" data-required="1" class="span8" value="{{ old('category_heading_'. $language->id) }}"/>
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Category Url * </label>
                            <div class="controls">
                                 <input type="text" name="category_url" id="category_url" data-required="1" class="span8" value="{{ old('category_url') }}"/>
								 <p class="general-note"> Note: valid characters are alpha numeric, - (dash) </p>
                             </div>
                           </div>

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $language->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="category_page_title_{{ $language->id }}" id="category_page_title" data-required="1" class="span8" value="{{ old('category_page_title_'. $language->id) }}"/>
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="category_meta_tags" id="category_meta_tags">{{ old('category_meta_tags') }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="category_meta_description" id="category_meta_description">{{ old('category_meta_description') }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0">Unpublished</option>
                                    <option value="1">Published</option>
                                 </select>

                              </div>

                           </div>
                            <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.categories') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
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
/**
  * Basic jQuery Validation Form Demo Code
  * Copyright Sam Deering 2012
  * Licence: http://www.jquery4u.com/license/
  */

  $(document).ready(function(){
    $('li').removeClass('active');
    $('#publication').addClass('active');
    $('#categories').addClass('active');

    $("#category_heading_2").blur(function(){
      var heading = $(this).val();
      var url = heading.trim().toLowerCase().replace(/[^a-z1-9]+/gi, '-');
      $("#category_url").val(url);
    });
  });

(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#addform").validate({
                rules: {
                    category_url: "required",
					<?php
						foreach($languages as $language){
							echo 'category_heading_'.$language->id.': "required",';
							echo 'category_description_'.$language->id.': "required",';
                   			//echo 'primary_contact_'.$language->id.':"required",	';
						}
					?>
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    category_url: "Please enter url of the category/page.",
					<?php
						foreach($languages as $language){
							echo 'category_heading_'.$language->id.': "Please enter the category heading in '.$language->language_name.'",';
							echo 'category_description_'.$language->id.': "Please enter the category description in '.$language->language_name.'",';
                   			//echo 'primary_contact_'.$language->id.':"Please enter the primary contact number in  '.$language->language_name.'",	';
						}
					?>

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
@endsection

@stop
