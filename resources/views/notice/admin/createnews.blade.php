@extends('layout.admin.masters')
@section('content')
@php
    use App\Models\Notice;
@endphp

        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Newsletter
							<small>Add newsletter.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.newsletters') }}">Newsletter Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Add Newsletter<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Add Newsletter</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">

@csrf
                           <div class="control-group">
                           <label class="control-label">Groups  * </label>
                          	<div class="controls">
                            <script type="text/javascript">
                                            $(document).ready(function() {
                                                $('#enableClickableOptGroups').multiselect({
                                                   enableFiltering: true,
                                                    enableClickableOptGroups: true,
                                                    includeSelectAllOption: true
                                                });
                                            });
                                        </script>
                                  {!! Notice::group_list_for_newsletterEntry() !!}
                             </div>
                           </div>

                          <div class="control-group">
                            <label class="control-label">Other Emails</label>
                            <div class="controls">
                              <select id="tokenize" name="more_emails[]" multiple="multiple" class="span12">
                              </select>
                              <p class="general-note">Email should be separated by comma.</p>
                            </div>
                          </div>

                          <div class="control-group">
                           <label class="control-label">Subject *</label>
                            <div class="controls">
                              <input type="text" name="newsletter_subject" id="newsletter_subject" data-required="1" class="span8" />
                            </div>
                           </div>



                           <div class="control-group">
                           <label class="control-label">Newsletter Details  * </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="newsletter_description">
								</textarea>
                            </div>
                           </div>

						<div class="control-group">
              <label class="control-label">Published Date *</label>
              <div class="controls">
                <input type="text" name="published_date" data-required="1" class="span8 date-picker" value="" />
              </div>
            </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                <img src="{{ $rel_site_url }}assets/admin/img/no-preview.png" alt="" />
                                            </div>
                                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div>
                                       <span class="btn btn-file btn-primary"><span class="fileupload-new">Select Attachment</span>
                                       <span class="fileupload-exists">Change</span>
                                       <input type="file" class="default" name="attachments[]" id="attchment" multiple/></span>
                                                <a href="#" class="btn  btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                <label class="error" id="backgroundvalid"></label>
                                            </div>
                                        </div>


                                    </div>
                          </div>


                            <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Send</button>
                              <a href="{{ route('admin.newsletters') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
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
<link rel="stylesheet" href="{{ asset('assets/admin/bootstrap-multiple/css/bootstrap-multiselect.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/admin/jquery-tokenize/jquery.tokenize.css') }}" type="text/css">
<script type="text/javascript" src="{{ asset('assets/admin/bootstrap-multiple/js/bootstrap-multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/admin/jquery-tokenize/jquery.tokenize.js') }}"></script>

<script type="text/javascript">
$('#tokenize').tokenize();
CKEDITOR.replace( 'newsletter_description',{
			filebrowserBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html',
			filebrowserImageBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Images',
			filebrowserFlashBrowseUrl : sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Flash',
			filebrowserUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
			filebrowserImageUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
			filebrowserFlashUploadUrl : sitePath + 'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
			filebrowserWindowWidth : '1000',
			filebrowserWindowHeight : '700'
		});
$(document).ready(function(){
  $('li').removeClass('active');
  $('#informations').addClass('active');
  $('#newsletters').addClass('active');
  $("#newsletter_heading").blur(function(){
    var heading = $(this).val();
    var url = heading.toLowerCase().trim().replace(/[^a-z0-9]+/gi, '-');
    $("#newsletter_url").val(url);
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
                  newsletter_url: "required",
                  published_date : "required",
					<?php

							echo 'newsletter_heading: "required",';
							echo 'newsletter_description: "required",';
											?>
					attachment:	{
						extension : 'jpe?g|png|doc|docx|pdf'
					}
                },
                messages: {
                  newsletter_url: "Please enter url of the newsletter/page.",
                  published_date: "Please enter published date newsletter/page.",
					<?php
							echo 'newsletter_heading: "Please enter the newsletter heading in ",';
							echo 'newsletter_description: "Please enter the newsletter description in ",';

					?>

					attachment : {
						extension	:	"Please select appropriate file. Only jpeg/jpg, doc/docx and pdf format are allowed."
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
