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
							Gallery
							<small>Edit gallery.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.albums') }}">Album Management</a> <span class="divider">&nbsp;</span>
                            </li>
							 <li>
                                <a href="{{ route('admin.album.view.gallery', $albumId) }}">Gallery Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Gallery<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.album.add.gallery', $albumId) }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Gallery</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Gallery Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
  @csrf
						@foreach($galleries as $gallery)
                           <div class="control-group">
                           <label class="control-label">Gallery Title ( {{ $gallery->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="gallery_title_{{ $gallery->language_id }}" data-required="1" class="span8"
									value="{{ $gallery->gallery_title}}" />
                             </div>
                           </div>
						@endforeach

						@foreach($galleries as $gallery)
                           <div class="control-group">
                           <label class="control-label">Gallery Caption ( {{ $gallery->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="span8" name="gallery_caption_{{ $gallery->language_id }}" >{{{ $gallery->gallery_caption}}}</textarea>
                            </div>
                           </div>
						@endforeach


                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                @include('helpers.attachment', ['src'=>$gallery->gallery_attachment,'folder'=>'gallery', 'showImageError'=>true])
                                            </div>
                                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div>
                                       <span class="btn btn-file btn-primary"><span class="fileupload-new">Select image</span>
                                       <span class="fileupload-exists">Change</span>
                                       <input type="file" class="default" name="attachment" id="attchment" /></span>
                                                <a href="#" class="btn  btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                <label class="error" id="backgroundvalid"></label>
                                                <p class="help-block">Image dimensions and aspect ratio are detected automatically. The image is resized proportionally without cropping or stretching. Original image is preserved. Maximum upload size: 2 MB.</p>
                                            </div>
                                        </div>
                                    </div>
                          </div>

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($gallery->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($gallery->is_active == '1' || $gallery->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.album.view.gallery', $albumId) }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $gallery->gallery_id}}}">

							@foreach($galleries as $gallery)
								<input type="hidden" name="detail_id_{{ $gallery->language_id }}" value="{{{ $gallery->gallery_detail_id}}}">
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
    $('#galleries').addClass('active');
    $('#albums').addClass('active');
    (function($, W, D) {
        var JQUERY4U = {};

        JQUERY4U.UTIL = {
            setupFormValidation: function() {
                //form validation rules
                $("#editform").validate({
                    rules: {
                        gallery_url: "required",
                        attachment: {
                            extension: 'jpe?g|png',
                            filesize: 2*1024*1024,
                        }
                    },
                    messages: {
                        gallery_url: "Please enter url of the gallery/page. ",

                        attachment: {
                            extension: "Please select appropriate file. Only jpeg/jpg and png format are allowed.",
                            filesize: "Please select image of size not greater than 2MB.",
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
