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
							<small>Edit vehicle.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.vehicles') }}">Vehicle Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Vehicle<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.vehicle.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Vehicle</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Vehicle Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($vehicles as $vehicle)
                           <div class="control-group">
                           <label class="control-label">Vehicle Type ( {{ $vehicle->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="vehicle_type_{{ $vehicle->language_id }}" data-required="1" class="span8" value="{{ $vehicle->vehicle_type }}" />
                             </div>
                           </div>
						@endforeach

						@foreach($vehicles as $vehicle)
                           <div class="control-group">
                           <label class="control-label">Vehicle Number ( {{ $vehicle->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="vehicle_num_{{ $vehicle->language_id }}" data-required="1" class="span8" value="{{ $vehicle->vehicle_num }}"/>
                             </div>
                           </div>
						@endforeach

						@foreach($vehicles as $vehicle)
                           <div class="control-group">
                           <label class="control-label">Condition ( {{ $vehicle->language_name }} ) * </label>
                          	<div class="controls">
                                <textarea class="span8" name="vehicle_remarks_{{ $vehicle->language_id }}" >{{{ $vehicle->vehicle_condition }}}</textarea>
                            </div>
                           </div>
						@endforeach


                           <div class="control-group">
                              <label class="control-label">Codition Type</label>
                              <div class="controls">
                                 <select name="condition_type" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($vehicleData->condition_type == '0')? 'selected="selected"' : '' ?> >stopped</option>
                                    <option value="1" <?php echo (($vehicleData->condition_type == '1' || $vehicleData->condition_type == '')? 'selected="selected"' : '') ?>>Running</option>
                                 </select>
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($vehicleData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($vehicleData->is_active == '1' || $vehicleData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.vehicles') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $vehicleData->id}}}">

							@foreach($vehicles as $vehicle)
								<input type="hidden" name="detail_id_{{ $vehicle->language_id }}" value="{{{ $vehicle->vehicle_detail_id }}}">
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
  $('#informations').addClass('active');
  $('#vehicles').addClass('active');
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
                    vehicle_amount: "required",
                    vehicle_received_date: "required",
					<?php
						foreach($languages as $language){
							echo 'vehicle_type_'.$language->id.': "required",';

						}
					?>
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    vehicle_amount: "Please enter amount of the vehicle.",
                    vehicle_received_date: "Please enter received date of the vehicle.",
					<?php
						foreach($languages as $language){
							echo 'vehicle_type_'.$language->id.': "Please enter the vehicle particulars in '.$language->language_name.'",';


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
</script>
@endsection
@stop
