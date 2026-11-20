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
							Training
							<small>Edit training.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.trainings') }}">Training Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Training<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.training.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Training</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Training Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Heading ( {{ $training->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="training_heading_{{ $training->language_id }}" data-required="1" class="span8"
									value="{{ $training->training_heading}}" />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Training Url *</label>
                          	<div class="controls">
                                 <input type="text" name="training_url" id="training_url" data-required="1" class="span8" value="{{ $trainingData->training_url }}"/>
                             </div>
                        </div>

						<div class="control-group">
                           <label class="control-label">Training Start Date * </label>
                          	<div class="controls">
                                 <input type="text" name="training_start_date" data-required="1" class="span8 date-picker" value="{{ $trainingData->training_start_date}}" />
                             </div>
            </div>

            <div class="control-group">
                           <label class="control-label">Training End Date * </label>
                          	<div class="controls">
                                 <input type="text" name="training_end_date" data-required="1" class="span8 date-picker" value="{{ $trainingData->training_end_date}}" />
                             </div>
            </div>

						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Description ( {{ $training->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="training_description_{{ $training->language_id }}">
                                   {{{ $training->training_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Contact Person ( {{ $training->language_name }} )  </label>
                          	<div class="controls">
                                 <input type="text" name="training_contact_person_{{ $training->language_id }}" data-required="1" class="span8" value="{{ $training->training_contact_person}}" />
                             </div>
                           </div>
						@endforeach

						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Contact Number ( {{ $training->language_name }} )  </label>
                          	<div class="controls">
                                 <input type="text" name="training_contact_number_{{ $training->language_id }}" data-required="1" class="span8" value="{{ $training->training_contact_number}}" />
                             </div>
                           </div>
						@endforeach

						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Remarks ( {{ $training->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="span8" name="training_remarks_{{$training->language_id }}" id="training_remarks">{{{ $training->training_remarks }}}</textarea>
                             </div>
                           </div>
						@endforeach

						@foreach($trainings as $training)
                           <div class="control-group">
                           <label class="control-label">Training Page Title ( {{ $training->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="training_page_title_{{ $training->language_id }}" data-required="1" class="span8" value="{{ $training->training_page_title}}" />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="training_meta_tags" id="training_meta_tags">{{{ $trainingData->training_meta_tags }}}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="training_meta_description" id="training_meta_description">{{{ $trainingData->training_meta_description }}}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                           <label class="control-label">Number Of Trainees </label>
                            <div class="controls">
                                 <input type="number" name="number_of_traniees" data-required="1" class="span8" value="{{ $trainingData->number_of_traniees }}" />
                             </div>
                           </div>

						<div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($trainingData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($trainingData->is_active == '1' || $trainingData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.trainings') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $trainingData->id}}}">

							@foreach($trainings as $training)
								<input type="hidden" name="detail_id_{{ $training->language_id }}" value="{{{ $training->training_detail_id}}}">
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
  $('#misc').addClass('active');
  $('#trainings').addClass('active');
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
                    training_url: "required",
                    training_start_date: "required",
                    training_end_date: "required",
          					attachment:	{
          						extension	:'jpe?g|png'
          					},
                    <?php
                    foreach($languages as $language){
                      echo 'training_heading_'.$language->language_code.': "required",';
                      echo 'training_description_'.$language->language_code.': "required",';
                    }
                    ?>
                },
                messages: {
                    training_url: "Please enter url of the training.",
                    training_start_date: "Please enter start date of the training.",
                    training_end_date: "Please enter end date of the training.",
					          attachment		:	{
											extension	:	"Please select appropriate file. Only jpeg/jpg and png format are allowed."
										},
                    <?php
                    foreach($languages as $language){
                      echo 'training_heading_'.$language->language_code.': "Please enter the heading in '.$language->language_name.'",';
                      echo 'training_description_'.$language->language_code.': "Please enter the Description in '.$language->language_name.'",';
                    }
                    ?>
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
