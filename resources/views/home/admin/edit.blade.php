@extends('layout.admin.masters')
@section('content')
@php
    // dd($home);
@endphp

        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Homes
							<small>Edit home.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.homes') }}">Home Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Home<span class="divider-last">&nbsp;</span></li>

						<?php /*
							@if($addAccess)
                            <a href="{{ $abs_site_url }}admin/home/add"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Home</button></a>
                            @endif
							*/ ?>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Edit Home Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                    <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($homes as $home)
                           <div class="control-group">
                           <label class="control-label">Home Heading ( {{ $home->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="home_heading_{{ $home->language_id }}" data-required="1" class="span8"
									value="{{ $home->home_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Home Url</label>
                            <div class="controls">
                                 <input type="text" disabled="disabled" name="home_url" id="home_url" data-required="1" class="span8"
								 value="{{ $home->home_url }}" />
                             </div>
                           </div>

						@if(in_array($the_home->home_url, [
						    'dor-spoke-person',
						    'general-info',
						    'information-officer',
						    'grievance-hearing-officer',
						    'additional-officer'
						], true))
						    @foreach($homes as $home)
						        <div class="control-group">
						            <label class="control-label">
						                Officer Name ({{ $home->language_name }})
						            </label>
						            <div class="controls">
						                <input type="text"
						                    name="officer_name_{{ $home->language_id }}"
						                    value="{{ $home->officer_name }}"
						                    class="span8" />
						            </div>
						        </div>
						    @endforeach
						@endif
						@foreach($homes as $home)
                           <div class="control-group">
                           <label class="control-label">Home Description ( {{ $home->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="home_description_{{ $home->language_id }}">
                                   {{ $home->home_description }}
								</textarea>
                            </div>
                           </div>
						@endforeach

                        @include('home.admin.extra_data', ['code'=>$home->home_url])

						@foreach($homes as $home)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $home->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="home_page_title_{{ $home->language_id }}" data-required="1" class="span8"
								 value=" {{ $home->home_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="home_meta_tags" >{{ $home->home_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="home_meta_description" id="home_meta_description">{{ $home->home_meta_description }}</textarea>
                            </div>
                        </div>

                           @if (preg_match('/^general-information(?:-[0-9]+)?$/', (string) $the_home->home_url))
                               {{-- Flash Box uses CKFinder fields only --}}
                           @elseif(in_array($the_home->home_url, ['message-from-director-general', 'dor-spoke-person', 'general-info', 'information-officer', 'grievance-hearing-officer', 'additional-officer'], true))
                               @include('admin.photo_studio.controls', ['photoStudioType' => 'home_officer'])
                           @else
<div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                       @include('helpers.attachment', ['src'=>$home->home_attachment,'folder'=>'pages'])
                                            </div>
                                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div>
                                       <span class="btn btn-file btn-primary"><span class="fileupload-new">Select image</span>
                                       <span class="fileupload-exists">Change</span>
                                       <input type="file" class="default" name="attachment" id="attchment" /></span>
                                                <a href="#" class="btn  btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                <label class="error" id="backgroundvalid"></label>
                                            </div>
                                        </div>
                                    </div>
                          </div>

                           @endif

<div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.homes') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $home->home_id}}}">

							@foreach($homes as $home)
								<input type="hidden" name="detail_id_{{ $home->language_id }}" value="{{{ $home->home_detail_id}}}">
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
@if(in_array($the_home->home_url, ['message-from-director-general', 'dor-spoke-person', 'general-info', 'information-officer', 'grievance-hearing-officer', 'additional-officer'], true))
    <script src="{{ asset('assets/admin/js/photo-studio.js') }}?v={{ filemtime(public_path('assets/admin/js/photo-studio.js')) }}"></script>
@endif


<script type="text/javascript">
  $('li').removeClass('active');
  $('#content').addClass('active');
  $('#homes').addClass('active');
<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'home_description_<?php echo $language->id ?>',{
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
                    home_url: "required",
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    home_url: "Please enter url of the home/page. ",

					attachment		:	{
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
