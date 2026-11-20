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
               Album
               <small>Edit album.</small>
            </h3>
            <ul class="breadcrumb">
               <li>
                  <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
               </li>
               <li>
                  <a href="{{ route('admin.albums') }}">Album Management</a> <span class="divider">&nbsp;</span>
               </li>
               <li>Edit Album<span class="divider-last">&nbsp;</span></li>
               @if($addAccess)
               <a href="{{ route('admin.album.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Album</button></a>
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
                     <h4><i class="icon-reorder"></i>Edit Album Form</h4>
                  </div>
                  <div class="widget-body form">
                     <div id="msgdisp">
                        @include('layout.admin.alert')
                     </div>

                     <form action="{{ route('admin.album.update', $album->album_id) }}" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
                        @foreach($albums as $album)
                        <div class="control-group">
                           <label class="control-label">Album Title ( {{ $album->language_name }} ) </label>
                           <div class="controls">
                              <input type="text" name="album_title_{{ $album->language_id }}" data-required="1" class="span8"
                                 value="{{ $album->album_title}}" />
                           </div>
                        </div>
                        @endforeach
                        <div class="control-group">
                           <label class="control-label">Album Url</label>
                           <div class="controls">
                              <input type="text" name="album_url" id="album_url" data-required="1" class="span8"
                                 value="{{ $album->album_url }}" />
                           </div>
                        </div>
                        @foreach($albums as $album)
                        <div class="control-group">
                           <label class="control-label">Album Caption ( {{ $album->language_name }} ) </label>
                           <div class="controls">
                              <textarea class="span8" name="album_caption_{{ $album->language_id }}" >{{{ $album->album_caption}}}</textarea>
                           </div>
                        </div>
                        @endforeach
                        <div class="control-group">
                           <label class="control-label">Attachment</label>
                           <div class="controls">
                              <div class="fileupload fileupload-new" data-provides="fileupload">
                                 <div class="fileupload-new thumbnail">
                                    @include('helpers.attachment', ['src'=>$album->album_attachment,'folder'=>'album'])
                                 </div>
                                 <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                 <div>
                                    <span class="btn btn-file btn-primary"><span class="fileupload-new">Select image</span>
                                    <span class="fileupload-exists">Change</span>
                                    <input type="file" class="default" name="attachment" id="attchment" value="{{$album->album_attachment}}" /></span>
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
                                 <option value="0" <?php echo ($album->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                 <option value="1" <?php echo (($album->is_active == '1' || $album->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                              </select>
                           </div>
                        </div>
                        <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                        <div class="form-actions">
                           <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                           <a href="{{ route('admin.albums') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                        </div>
                        <input type="hidden" name="id" value="{{{ $album->album_id}}}">
                        @foreach($albums as $album)
                        <input type="hidden" name="detail_id_{{ $album->language_id }}" value="{{{ $album->album_detail_id}}}">
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
                        album_url: "required",
                        attachment: {
                            extension: 'jpe?g|png',
                            filesize:1*1024*1024,
                        }
                    },
                    messages: {
                        album_url: "Please enter url of the album/page. ",

                        attachment: {
                            extension: "Please select appropriate file. Only jpeg/jpg and png format are allowed.",
                            filesize: "Please select image of size not greater than 1MB.",
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
