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
							Tender
							<small>Edit tender.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.tenders') }}">Tender Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Tender<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.tender.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Tender</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Tender Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                       @csrf()
						@foreach($tenders as $tender)
                           <div class="control-group">
                           <label class="control-label">Tender Heading ( {{ $tender->language_name }} ) * </label>
                          	<div class="controls">
                                 <input type="text" name="tender_heading_{{ $tender->language_id }}" data-required="1" class="span8"
									value="{{ $tender->tender_heading}}" id="tender_heading_{{ $tender->language_id }}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Tender Url *</label>
                            <div class="controls">
                                 <input type="text" name="tender_url" id="tender_url" data-required="1" class="span8"
								 value="{{ $tenderData->tender_url }}" />
                             </div>
                           </div>

						@foreach($tenders as $tender)
                           <div class="control-group">
                           <label class="control-label">Tender Description ( {{ $tender->language_name }} ) *</label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="tender_description_{{ $tender->language_id }}">
                                   {{{ $tender->tender_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($tenders as $tender)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $tender->language_name }} ) *</label>
                          	<div class="controls">
                                 <input type="text" name="tender_page_title_{{ $tender->language_id }}" data-required="1" class="span8"
								 value=" {{ $tender->tender_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
              <label class="control-label">Published Date *</label>
             	<div class="controls">
                <input type="text" name="published_date" data-required="1" class="span8 date-picker"
								 value=" {{ $tenderData->published_date }} " />
              </div>
            </div>

						<div class="control-group">
                           <label class="control-label">Expiry Date *</label>
                          	<div class="controls">
                                 <input type="text" name="expiry_date" data-required="1" class="span8 date-picker"
								 value=" {{ $tenderData->expiry_date }} " />
                             </div>
            </div>

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="tender_meta_tags" >{{ $tenderData->tender_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="tender_meta_description" id="tender_meta_description">{{ $tenderData->tender_meta_description }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <small>File must be:jpg|jpeg|pdf|zip<br> Size: less than 10 MB</small>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                       @include('helpers.attachment', ['src'=>$tenderData->tender_attachment,'folder'=>'tender'])
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
                                    <option value="0" <?php echo ($tenderData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($tenderData->is_active == '1' || $tenderData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.tenders') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $tenderData->id}}}">

							@foreach($tenders as $tender)
								<input type="hidden" name="detail_id_{{ $tender->language_id }}" value="{{{ $tender->tender_detail_id}}}">
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

<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'tender_description_<?php echo $language->id ?>',{
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

$('li').removeClass('active');
$('#informations').addClass('active');
$('#tenders').addClass('active');
$("#tender_heading_2").blur(function(){
  var heading = $(this).val();
  var permalink = heading.toLowerCase().trim().replace(/[^a-z0-9]+/gi, '-');
  //$("#tender_url").val(permalink);
});

(function($, W, D) {
    var JQUERY4U = {};

    JQUERY4U.UTIL = {
        setupFormValidation: function() {
            //form validation rules
            $("#editform").validate({
                rules: {
                    tender_url: "required",
                    expiry_date: "required",
                    <?php
                      foreach($languages as $language){
                        echo 'tender_heading_'.$language->id.': "required",';
                        echo 'tender_description_'.$language->id.': "required",';
                        echo 'published_date_'.$language->id.':"required",  ';
                      }
                    ?>
                    attachment: {
                        extension: 'jpe?g|zip|pdf',
                        filesize:10*1024*1024,
                    }
                },
                messages: {
                    tender_url: "Please enter url of the tender/page.",
                    expiry_date: "Please enter expiry date of tender.",
                    <?php
                      foreach($languages as $language){
                        echo 'tender_heading_'.$language->id.': "Please enter the tender heading in '.$language->language_name.'",';
                        echo 'tender_description_'.$language->id.': "Please enter the tender description in '.$language->language_name.'",';
                        echo 'published_date_'.$language->id.':"Please enter the publish date in  '.$language->language_name.'",  ';
                      }
                    ?>
                    attachment: {
                        extension: "Please select appropriate file. Only jpeg/jpg, zip and pdf format are allowed.",
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
