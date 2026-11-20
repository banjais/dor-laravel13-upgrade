@extends('layout.admin.masters')
@section('content')

<script>
	$('li').removeClass('active');
	$('#misc').addClass('active');
	$('#faqs').addClass('active');
</script>
<link rel="stylesheet" type="text/css" href="{{ $rel_site_url}}assets/admin/bootstrap-datepicker/css/datepicker.css" />

        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Faq
							<small>Edit faq.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="../../faqs">Faq Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Faq<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ $abs_site_url }}admin/faq/add"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Faq</button>
                            @endif
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
                        <h4><i class="icon-reorder"></i>Edit Faq Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                       @csrf
						@foreach($faqs as $faq)
                           <div class="control-group">
                           <label class="control-label">Faq Question ( {{ $faq->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="faq_question_{{ $faq->language_id }}" data-required="1" class="span8"
									value="{{ $faq->faq_question }}" />
                             </div>
                           </div>
						@endforeach

						@foreach($faqs as $faq)
                           <div class="control-group">
                           <label class="control-label">Faq Answer ( {{ $faq->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="faq_answer_{{ $faq->language_id }}">
                                   {{{ $faq->faq_answer }}}
								</textarea>
                            </div>
                           </div>
						@endforeach

						<div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($faq->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($faq->is_active == '1' || $faq->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ $abs_site_url }}admin/faqs"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $faq->faq_id}}}">

							@foreach($faqs as $faq)
								<input type="hidden" name="detail_id_{{ $faq->language_id }}" value="{{{ $faq->faq_detail_id}}}">
							@endforeach
                        </form>

                     </div>
                  </div>

               </div>
            </div>
              </div>

			</div>

		</div>


<script type="text/javascript">

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
                    faq_url: "required",
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    faq_url: "Please enter url of the faq/page. ",

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

	@stop
