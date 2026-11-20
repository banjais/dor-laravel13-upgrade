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
							Download
							<small>Edit download.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.downloads') }}">Download Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Download<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.download.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Download</button></a>
                            @endif
                        </ul>

					</div>
			  </div>


              <div id="page">

              <div class="row-fluid">
               <div class="span12">
                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Edit Download Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>
                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
 @csrf
						@foreach($downloads as $download)
                           <div class="control-group">
                           <label class="control-label">Download Heading ( {{ $download->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="download_heading_{{ $download->language_id }}" id="download_heading_{{ $download->language_id }}" data-required="1" class="span8"
									value="{{ $download->download_heading}}" />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Download Url</label>
                            <div class="controls">
                                 <input type="text" name="download_url" id="download_url" data-required="1" class="span8"
								 value="{{ $downloadData->download_url }}" />
                             </div>
            </div>

            <div class="control-group">
                           <label class="control-label">Published Date *</label>
                          	<div class="controls">
                                 <input type="text" name="published_date" data-required="1" class="span8 date-picker"
								 value=" {{ $downloadData->published_date }} " />
                             </div>
            </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <small>File must be:jpg|jpeg|pdf|zip<br> Size: less than 10 MB</small>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                @include('helpers.attachment', ['src'=>$downloadData->download_attachment,'folder'=>'download'])
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
                                    <option value="0" <?php echo ($downloadData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($downloadData->is_active == '1' || $downloadData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.downloads') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $downloadData->id}}}">

							@foreach($downloads as $download)
								<input type="hidden" name="detail_id_{{ $download->language_id }}" value="{{{ $download->download_detail_id}}}">
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
  $('#downloads').addClass('active');

(function($, W, D) {
    var JQUERY4U = {};

    JQUERY4U.UTIL = {
        setupFormValidation: function() {
            //form validation rules
            $("#editform").validate({
                rules: {
                    download_url: "required",
                    published_date: "required",
                    <?php
                      foreach($languages as $language){
                        echo 'download_heading_'.$language->id.': "required",';
                      }
                    ?>
                    attachment: {
                        extension: 'jpe?g|zip|pdf',
                        filesize:10*1024*1024,
                    }
                },
                messages: {
                    download_url: "Please enter url of the download.",
                    published_date: "Please enter published date of the download.",
                    <?php
                      foreach($languages as $language){
                        echo 'download_heading_'.$language->id.': "Please enter the download heading in '.$language->language_name.'",';
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
