@extends('layout.admin.masters')
@section('content')

<script>
	$('li').removeClass('active');
	$('#misc').addClass('active');
	$('#seos').addClass('active');
</script>


        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Seo
							<small>Add seo.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="../home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="../seos">Seo Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Add Seo<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->


              </a>
              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Add Seo Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<?php if(Session::get('class')) { ?>
						<div class="<?php echo $class = Session::get('class');?>">
							<button class="close" data-dismiss="alert">x</button>
							<?php
								echo $message = Session::get('message');
								if(Session::get('attachment-error')) {
									echo Session::get('attachment-error');
								}
							?>
						</div>
						<?php } ?>

                        <form action="{{ $abs_site_url }}admin/seo/add" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">
                        @csrf
						   <div class="control-group">
                           <label class="control-label">Page Name * </label>
                          	<div class="controls">
                                 <input type="text" name="page_name" data-required="1" class="span8" />
                             </div>
                           </div>

						@foreach($languages as $language)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $language->language_name }} ) * </label>
                          	<div class="controls">
                                <textarea class="span8" name="page_title_{{ $language->id }}" id="page_title"></textarea>
                            </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags * </label>
                          	<div class="controls">
                                 <textarea class="span8" name="meta_tags" id="meta_tags"></textarea>
                             </div>
                           </div>

						   <div class="control-group">
                           <label class="control-label">Meta Description * </label>
                          	<div class="controls">
                                 <textarea class="span8" name="meta_description" id="meta_description"></textarea>
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
                              <a href="{{ $abs_site_url }}admin/seos"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
                        </form>

                     </div>
                  </div>

               </div>
            </div>
              </div>

			</div>

		</div>


<script type="text/javascript">
/**
  * Basic jQuery Validation Form Demo Code
  * Copyright Sam Deering 2012
  * Licence: http://www.jquery4u.com/license/
  */

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
                    page_name: "required",
                    meta_tags: "required",
                    meta_description: "required",
					<?php
						foreach($languages as $language){
							echo 'page_title_'.$language->id.': "required",';
						}
					?>
                },
                messages: {
                    page_name: "Please enter name of page.",
                    meta_tags: "Please enter meta tags of page.",
                    meta_description: "Please enter meta description of page.",
					<?php
						foreach($languages as $language){
							echo 'page_title_'.$language->id.': "Please enter the page title in '.$language->language_name.'",';
						}
					?>
                },
                /*submitHandler: function(form) {
					$('#addform').ajaxSubmit({
					url   :  $('#addform').attr('action'),
					type  :  "POST",
					beforeSend : function(){
									$('#ajax-notifier-background').css('display','block');
								},
					success : function(data){
								$json= JSON.parse(data);
								type= $json.type;
								message=$json.message;
								if(type=="success")
								{
									$('#msgdisp1').append('<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>'+ message+'</div>');
									$('#addform')[0].reset();
								}
								else if(type=='error'){
									$('#msgdisp1').append('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>'+ message +'</div>');
								}
							},
					complete : function(){
									$('#ajax-notifier-background').css('display','none');
								},
					error : function(e){
								alert("Error occured. Please contact the company developer.");
							  }
					});
				}*/
			});

        }

    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);
</script>

	@stop
