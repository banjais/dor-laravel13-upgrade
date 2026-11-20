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
							Guidelines
							<small>Edit guideline.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.guidelines') }}">Guidelines Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Guideline<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.guideline.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Guideline</button></a>
                            @endif
                        </ul>
					</div>
			  </div>
              <div id="page">

              <div class="row-fluid">
               <div class="span12">
                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Edit Guideline Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>
            <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($guidelines as $guideline)
                           <div class="control-group">
                           <label class="control-label">Guideline Heading ( {{ $guideline->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="guideline_heading_{{ $guideline->language_id }}" data-required="1" class="span8"
									value="{{ $guideline->guideline_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Notice Url</label>
                            <div class="controls">
                                 <input type="text" name="guideline_url" id="guideline_url" data-required="1" class="span8"
								 value="{{ $guidelineData->guideline_url }}" />
                             </div>
                           </div>
						@foreach($guidelines as $guideline)
                           <div class="control-group">
                           <label class="control-label">Guideline Description ( {{ $guideline->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="guideline_description_{{ $guideline->language_id }}">
                                   {{{ $guideline->guideline_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($guidelines as $guideline)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $guideline->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="guideline_page_title_{{ $guideline->language_id }}" data-required="1" class="span8"
								 value=" {{ $guideline->guideline_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
              <label class="control-label">Published Date *</label>
             	<div class="controls">
                <input type="text" name="published_date" data-required="1" class="span8 date-picker"
								 value=" {{ $guidelineData->published_date }} " />
              </div>
            </div>

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="guideline_meta_tags" >{{ $guidelineData->guideline_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="guideline_meta_description" id="guideline_meta_description">{{ $guidelineData->guideline_meta_description }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <small>File must be:jpg|jpeg|pdf|zip|rar<br> Size: less than 10 MB</small>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                       @include('helpers.attachment', ['src'=>$guidelineData->guideline_attachment,'folder'=>'guideline'])
                                            </div>
                                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div>
                                       <span class="btn btn-file btn-primary"><span class="fileupload-new">Select Attachment</span>
                                       <span class="fileupload-exists">Change</span>
                                       <input type="file" class="default" name="attachment" id="attchment" /></span>
                                                <a href="#" class="btn  btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                <label class="error" id="backgroundvalid"></label>
                                            </div>
                                        </div>
                                    </div>
                          </div>

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($guidelineData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($guidelineData->is_active == '1' || $guidelineData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.guidelines') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $guidelineData->id}}}">

							@foreach($guidelines as $guideline)
								<input type="hidden" name="detail_id_{{ $guideline->language_id }}" value="{{{ $guideline->guideline_detail_id}}}">
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
  $('#guidelines').addClass('active');
<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'guideline_description_<?php echo $language->id ?>',{
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
                    guideline_url: "required",
                    published_date : "required",
          <?php
            foreach($languages as $language){
              echo 'guideline_heading_'.$language->id.': "required",';
              echo 'guideline_description_'.$language->id.': "required",';
            }
          ?>
					attachment:	{
						extension : 'jpe?g|zip|rar|pdf',
            filesize:100*1024*1024,
					}
                },
                messages: {
                  guideline_url: "Please enter url of the guideline/page. ",
					        published_date: "Please enter published date guideline/page.",
          <?php
            foreach($languages as $language){
              echo 'guideline_heading_'.$language->id.': "Please enter the guideline heading in '.$language->language_name.'",';
              echo 'guideline_description_'.$language->id.': "Please enter the guideline description in '.$language->language_name.'",';
            }
          ?>
					attachment : {
						extension	:	"Please select appropriate file. Only jpeg/jpg, zip, rar and pdf format are allowed.",
            filesize: "Please select attachment of size not greater than 100MB.",
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
