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
							News
							<small>Edit news.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.news') }}">News Ticker Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit News<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ url('admin/news-ticker/add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add News Ticker</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit News Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>


            <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                @csrf
						@foreach($newss as $news)
                           <div class="control-group">
                           <label class="control-label">News Ticker Title ( {{ $news->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="ticker_heading_{{ $news->language_id }}" id="news_heading_{{ $news->language_id }}" data-required="1" class="span8"
									value="{{ $news->ticker_title}}" />
                             </div>
                           </div>
						@endforeach



						@foreach($newss as $news)
                           <div class="control-group">
                           <label class="control-label">News Ticker Description ( {{ $news->language_name }} ) </label>
                          	<div class="controls">
                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20" name="ticker_description_{{ $news->language_id }}">
                                   {{{ $news->ticker_description }}}
								</textarea>
                            </div>
                           </div>
						@endforeach






                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($newsData->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($newsData->is_active == '1' || $newsData->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.news') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $newsData->id}}}">

							@foreach($newss as $news)
								<input type="hidden" name="detail_id_{{ $news->language_id }}" value="{{{ $news->ticker_detail_id}}}">
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
CKEDITOR.replace( 'ticker_description_<?php echo $language->id ?>',{
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

$(document).ready(function(){
  $('li').removeClass('active');
  $('#informations').addClass('active');
  $('#news').addClass('active');
  $('#news_heading_2').blur(function(){
    var heading = $(this).val();
    var url = heading.toLowerCase().trim().replace(/[^a-z0-9]+/gi, '-');
    //$("#news_url").val(url);
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
      $("#editform").validate({
        rules: {
          news_url: "required",
          published_date: "required",
					attachment:	{
						extension : 'jpe?g|png|doc|docx|pdf'
					}
        },
        messages: {
          news_url: "Please enter url of the news. ",
          published_date: "Please enter published date of the news.",
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
