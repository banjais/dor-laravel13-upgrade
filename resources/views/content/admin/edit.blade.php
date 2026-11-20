@extends('layout.admin.masters')
@section('content')
@php
    use App\Models\Content;
@endphp

        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Contents
							<small>Add content.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.contents') }}">Content Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Content<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.content.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Content</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Content Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                            @csrf
                        @foreach($contents as $content)
						@endforeach
						<div class="control-group">
                              <label class="control-label">Parent</label>
                              <div class="controls">
                                 {!! Content::content_list_for_contentEntry($officeId, 0,0,$content->parent_id) !!}
                            </div>
                        </div>

						@foreach($contents as $content)
                           <div class="control-group">
                           <label class="control-label">Content Heading ( {{ $content->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="content_heading_{{ $content->language_id }}" data-required="1" class="span8"
									value="{{ $content->content_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Content Url</label>
                            <div class="controls">
                                 <input type="text" name="content_url" id="content_url" data-required="1" class="span8"
								 value="{{ $content->content_url }}" />
                             </div>
                           </div>

						@foreach($contents as $content)
                           <div class="control-group">
                           <label class="control-label">Content Description ( {{ $content->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="content_description_{{ $content->language_id }}">
                                   {{{ $content->content_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						@foreach($contents as $content)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $content->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="content_page_title_{{ $content->language_id }}" data-required="1" class="span8"
								 value=" {{ $content->content_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="content_meta_tags" >{{ $content->content_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="content_meta_description" id="content_meta_description">{{ $content->content_meta_description }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail">
                                                       @include('helpers.attachment', ['src'=>$content->content_attachment,'folder'=>'pages'])
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
<div id="thisdiv">
                                        <div class="controls" >
   {!! Content::content_list_for_attachment($content->content_id) !!}
                                        <div class="input_fields_wrap">
    <button class="add_field_button">Add More Attachment </button><small>( * Please use file size Maximum 5 MB.)</small>
    <div><input type="text" name="file_name[]" placeholder=" Document title"/><input type="file" name="file_attachment[]"></div>
</div>

                                    </div>
                                    </div>

                          </div>

						  <div class="control-group">
                              <label class="control-label">Show In</label>
                              <div class="controls">
                                 <?php	$globalArrayMenuPosition = array('Header' => '1', 'Footer' => '2');
                                        $arrayPrevSelected = explode(",",$content->show_in);foreach($globalArrayMenuPosition as $menuTitle => $singleMenuPosition) {
                                        	$selectedText = in_array($singleMenuPosition,$arrayPrevSelected)?'checked="checked"':'';
                                  ?>
									<input type="checkbox" name="show_in[]" value="<?php echo $singleMenuPosition?>" <?php echo $selectedText;?> />&nbsp;<?php echo $menuTitle?>&nbsp;&nbsp;
                                   <?php } ?>
                              </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">Order</label>
                            <div class="controls">
                                  <input type="number" name="order_menu" value="{{ $content->order_menu }}"  />
                             </div>
                           </div>

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($content->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($content->is_active == '1' || $content->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.contents') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $content->content_id}}}">

							@foreach($contents as $content)
								<input type="hidden" name="detail_id_{{ $content->language_id }}" value="{{{ $content->content_detail_id}}}">
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
$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

  $('li').removeClass('active');
  $('#content').addClass('active');
  $('#contents').addClass('active');
$(document).ready(function() {
   $("#content_heading_2").blur(function(){
    var heading = $(this).val();
    var url = heading.toLowerCase().trim().replace(/[^a-z0-9]+/gi, '-');
    $("#content_url").val(url);
  });
    var max_fields      = 30; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><input type="text" name="file_name[]" placeholder=" Document title" /><input type="file" name="file_attachment[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })


});


<?php foreach($languages as $language) { ?>
CKEDITOR.replace( 'content_description_<?php echo $language->id ?>',{
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
                    content_url: "required",
					attachment:	{
									extension	:'jpe?g|png|pdf|doc|docx'
								}
                },
                messages: {
                    content_url: "Please enter url of the content/page. ",

					attachment		:	{
											extension	:	"Please select appropriate file. Only jpeg/jpg, png, pdf and doc file formats are allowed."
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
  $(document).on('click','.delete-content',function(){

        $datatosend = "id="+this.id;
        $object = $(this);
        var con = confirm("Are you sure you want to delete this file.");
        if(con)
        {
          $.ajax({
            url   : "{{ $abs_site_url }}admin/content_document/delete",
            type  : "POST",
            data  : $datatosend,
            beforeSend : function(){
                    $('#ajax-notifier-background').css('display','block');
                  },
            success : function(response){
                    $json= JSON.parse(response);
                    type= $json.type;
                    message=$json.message;
                    if(type=="success") {
                      $('#msgdisp').empty();
                      $('#msgdisp').append('<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>'+ message+'</div>');
                       $('#thisdiv').load(document.URL +  ' #thisdiv');

                    }
                  },
            complete: function(){
                    $('#ajax-notifier-background').css('display','none');
                  },
            error : function(){
                    alert("This record couldnot be deleted due to of unknown error. Please try again. ");
                  }
          });
        }

    })
</script>
@endsection

	@stop
