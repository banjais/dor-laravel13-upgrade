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
							Circular
							<small>Edit circular.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.circulars') }}">Circular Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Circular<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.circular.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Circular</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Circular Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

            <form action="{{ route('admin.circular.update', $circularData->id) }}" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                      @csrf
						@foreach($circulars as $circular)
                           <div class="control-group">
                           <label class="control-label">Circular Heading ( {{ $circular->language_name }} ) *</label>
                          	<div class="controls">
                                 <input type="text" name="circular_heading_{{ $circular->language_id }}" data-required="1" class="span8"
									value="{{ $circular->circular_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Circular Url *</label>
                            <div class="controls">
                                 <input type="text" name="circular_url" id="circular_url" data-required="1" class="span8"
								 value="{{ $circularData->circular_url }}" />
                             </div>
                           </div>

						@foreach($circulars as $circular)
                           <div class="control-group">
                           <label class="control-label">Circular Description ( {{ $circular->language_name }} ) *</label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="circular_description_{{ $circular->language_id }}">
                                   {{{ $circular->circular_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($circulars as $circular)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $circular->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="circular_page_title_{{ $circular->language_id }}" data-required="1" class="span8"
								 value=" {{ $circular->circular_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
              <label class="control-label">Published Date *</label>
             	<div class="controls">
                <input type="text" name="published_date" data-required="1" class="span8 date-picker"
								 value=" {{ $circularData->published_date }} " />
              </div>
            </div>

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="circular_meta_tags" >{{ $circularData->circular_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="circular_meta_description" id="circular_meta_description">{{ $circularData->circular_meta_description }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <small>Allowed: PNG, JPG, JPEG, PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR<br> Maximum size: 100 MB</small>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                       @include('helpers.attachment', ['src'=>$circularData->circular_attachment,'folder'=>'circular'])
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
                                    <option value="0" <?php echo ($circularData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($circularData->is_active == '1' || $circularData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.circulars') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $circularData->id}}}">

							@foreach($circulars as $circular)
								<input type="hidden" name="detail_id_{{ $circular->language_id }}" value="{{{ $circular->circular_detail_id}}}">
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
  $('#circulars').addClass('active');
<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'circular_description_<?php echo $language->id ?>',{
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
                  circular_url: "required",
                  published_date : "required",
                  <?php
                    foreach($languages as $language){
                      echo 'circular_heading_'.$language->id.': "required",';
                      echo 'circular_description_'.$language->id.': "required",';
                    }
                  ?>
        					attachment:	{
                                                      extension : 'png|jpe?g|pdf|docx?|pptx?|xlsx?|zip|rar',
                    filesize:100*1024*1024,
        					}
                },
                messages: {
                    circular_url: "Please enter url of the circular/page. ",
                    published_date: "Please enter published date circular/page.",
                    <?php
                      foreach($languages as $language){
                        echo 'circular_heading_'.$language->id.': "Please enter the circular heading in '.$language->language_name.'",';
                        echo 'circular_description_'.$language->id.': "Please enter the circular description in '.$language->language_name.'",';
                      }
                    ?>
          					attachment : {
                                                      extension	:	"Please select appropriate file. PNG, JPG, JPEG, PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP and RAR formats are allowed.",
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
