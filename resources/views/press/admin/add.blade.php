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
							Press
							<small>Add press.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.presses') }}">Press Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Add Press<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Add Press Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">
@csrf
						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Press Heading ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="press_heading_{{ $language->id }}" id="press_heading_{{ $language->id }}" value="{{ old('press_heading_'.$language->id) }}" data-required="1" class="span8" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Press Url * </label>
                            <div class="controls">
                                 <input type="text" name="press_url" id="press_url" data-required="1" class="span8" value="{{ old('press_url') }}" />
								 <p class="general-note"> Note: valid characters are alpha numeric, - (dash) and _ (underscore)</p>
                             </div>
                           </div>

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Press Description ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="press_description_{{ $language->id }}">
								                {{ old('press_description_'.$language->id) }}</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $language->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="press_page_title_{{ $language->id }}" id="press_page_title" value="{{ old('press_page_title_'.$language->id) }}" data-required="1" class="span8" />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
              <label class="control-label">Published Date *</label>
                <div class="controls">
                  <input type="text" name="published_date" id="published_date" data-required="1" class="span8 date-picker" value="{{ old('published_date') }}"/>
              </div>
            </div>

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="press_meta_tags" id="press_meta_tags">{{ old('press_meta_tags') }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="press_meta_description" id="press_meta_description">{{ old('press_meta_description') }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <small>File must be:jpg|jpeg|pdf|zip<br> Size: less than 10 MB</small>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                <img src="{{ $rel_site_url }}assets/admin/img/no-image.png" alt="" />
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
                                    <option value="0">Unpublished</option>
                                    <option value="1">Published</option>
                                 </select>

                              </div>

                           </div>
                            <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.presses') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
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

<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'press_description_<?php echo $language->id ?>',{
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

$(document).ready(function(){
  $('li').removeClass('active');
  $('#informations').addClass('active');
  $('#presses').addClass('active');
  $('#press_heading_2').blur(function(){
    var heading = $(this).val();
    var url = heading.trim().toLowerCase().replace(/[^a-z0-9]+/gi, '-');
    $('#press_url').val(url);
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
                    press_url: "required",
                    published_date: "required",
					<?php
						foreach($languages as $language){
							echo 'press_heading_'.$language->id.': "required",';
							echo 'press_description_'.$language->id.': "required",';
						}
					?>
					attachment:	{
						extension : 'jpe?g|zip|pdf',
            filesize:10*1024*1024,
					}
                },
                messages: {
                    press_url: "Please enter url of the press.",
                    published_date: "Please enter published date of the press.",
					<?php
						foreach($languages as $language){
							echo 'press_heading_'.$language->id.': "Please enter the press heading in '.$language->language_name.'",';
							echo 'press_description_'.$language->id.': "Please enter the press description in '.$language->language_name.'",';
            }
					?>

					attachment : {
						extension	:	"Please select appropriate file. Only jpeg/jpg, zip and pdf format are allowed.",
            filesize: "Please select attachment of size not greater than 10MB.",
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
