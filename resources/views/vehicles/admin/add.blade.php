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
							Vehicle
							<small>Add vehicle.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="../home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="../vehicles">Vehicle Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Add Vehicle<span class="divider-last">&nbsp;</span></li>
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
                        <h4><i class="icon-reorder"></i>Add Vehicle Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

            <form action="" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">
@csrf
						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Vehicle Type ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="vehicle_type_{{ $language->id }}" data-required="1" class="span8" value="{{ old('vehicle_type_'.$language->id) }}"/>
                             </div>
                           </div>
						@endforeach

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Vehicle Number ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="vehicle_num_{{ $language->id }}" data-required="1" class="span8" value="{{ old('vehicle_num_'.$language->id) }}"/>
                             </div>
                           </div>
						@endforeach

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Condition ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12" rows="5" cols="20" name="vehicle_remarks_{{ $language->id }}">{{ old('vehicle_remarks_'.$language->id) }}</textarea>
                            </div>
                           </div>
						@endforeach

			                           <div class="control-group">
                              <label class="control-label">Codition Type</label>
                              <div class="controls">
                                 <select name="condition_type" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="condition_type">
                                    <option value="1">Running </option>
                                    <option value="0">Stopped</option>
                                 </select>

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
                              <a href="{{ route('admin.vehicles') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
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
  $('li').removeClass('active');
  $('#informations').addClass('active');
  $('#vehicles').addClass('active');
/**
  * Basic jQuery Validation Form Demo Code
  * Copyright Sam Deering 2012
  * Licence: http://www.jquery4u.com/license/
  */
 <?php /*
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

					<?php
						foreach($languages as $language){
							echo 'vehicle_type_'.$language->id.': "required",';
							echo 'vehicle_num_'.$language->id.': "required",';
							echo 'vehicle_remarks_'.$language->id.': "required",';
						}
					?>
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    vehicle_amount: "Please enter amount of the vehicle.",
                    vehicle_received_date: "Please enter received date of vehicle.",
					<?php
						foreach($languages as $language){
							echo 'vehicle_type_'.$language->id.': "Please enter the vehicle particulars in '.$language->language_name.'",';
							echo 'vehicle_num_'.$language->id.':"Please enter the vehicle expense head in '.$language->language_name.'",';
							echo 'vehicle_remarks_'.$language->id.': "Please enter the vehicle buying head in '.$language->language_name.'",';


						}
					?>

					attachment	:{
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
*/ ?>
</script>
@endsection
@stop
