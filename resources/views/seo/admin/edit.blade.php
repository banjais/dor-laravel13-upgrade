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
							Seo
							<small>Edit seo.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.seos') }}">Seo Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Seo<span class="divider-last">&nbsp;</span></li>
							<?php /*
                            @if($addAccess)
                            <a href="{{ $abs_site_url }}admin/seo/add"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Seo</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Seo Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                           @csrf
						   @foreach($seos as $seo)

						   @endforeach

						<div class="control-group">
                           <label class="control-label">Page Name </label>
                          	<div class="controls">
                                 <input type="text" readonly="readonly" name="page_name" data-required="1" class="span8"
									value="{{ $seo->page_name }}" />
                             </div>
                           </div>

						@foreach($seos as $seo)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $seo->language_name }} ) *</label>
                          	<div class="controls">
                                <textarea class="span8" name="page_title_{{ $seo->language_id }}" id="page_title_{{ $seo->language_id }}">{{ $seo->page_title }}</textarea>
                            </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags * </label>
                          	<div class="controls">
                                 <textarea class="span8" name="meta_tags" id="meta_tags">{{ $seo->meta_tags }}</textarea>
                             </div>
                           </div>

						   <div class="control-group">
                           <label class="control-label">Meta Description * </label>
                          	<div class="controls">
                                 <textarea class="span8" name="meta_description" id="meta_description">{{ $seo->meta_description }}</textarea>
                             </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.seos') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $seo->seo_id}}}">

							@foreach($seos as $seo)
								<input type="hidden" name="detail_id_{{ $seo->language_id }}" value="{{{ $seo->seo_detail_id}}}">
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
  $('#seos').addClass('active');
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
                  meta_tags: "required",
                  meta_description: "required",
                    <?php
            foreach($languages as $language){
              echo 'page_title_'.$language->id.': "required",';
            }
          ?>

                },
                messages: {
                    meta_tags: "Please enter meta tags of the seo/page. ",
                    meta_description: "Please enter meta description of the seo/page. ",
					         <?php
                    foreach($languages as $language){
                      echo 'page_title_'.$language->id.': "Please enter the page title in '.$language->language_name.'",';
                    }
                  ?>
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
