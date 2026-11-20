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
							Category
							<small>Edit category.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.categories') }}">Category Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>Edit Category<span class="divider-last">&nbsp;</span></li>
                            @if($addAccess)
                            <a href="{{ route('admin.category.add') }}"><button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Add Category</button></a>
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
                        <h4><i class="icon-reorder"></i>Edit Category Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        @include('layout.admin.alert')
                      </div>

                        <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                        @csrf
						@foreach($categories as $category)
                           <div class="control-group">
                           <label class="control-label">Category Heading ( {{ $category->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="category_heading_{{ $category->language_id }}" data-required="1" class="span8"
									value="{{ $category->category_heading}}" />
                             </div>
                           </div>
						@endforeach

                           <div class="control-group">
                           <label class="control-label">Category Url</label>
                            <div class="controls">
                                 <input type="text" name="category_url" id="category_url" data-required="1" class="span8"
								 value="{{ $category->category_url }}" />
                             </div>
                           </div>

						@foreach($categories as $category)
                           <div class="control-group">
                           <label class="control-label">Page Title ( {{ $category->language_name }} ) </label>
                          	<div class="controls">
                                 <input type="text" name="category_page_title_{{ $category->language_id }}" data-required="1" class="span8"
								 value=" {{ $category->category_page_title }} " />
                             </div>
                           </div>
						@endforeach

						<div class="control-group">
                           <label class="control-label">Meta Tags </label>
                          	<div class="controls">
                                <textarea class="span8" name="category_meta_tags" >{{ $category->category_meta_tags }}</textarea>
                             </div>
                           </div>

						 <div class="control-group">
                           <label class="control-label">Meta Description </label>
                          	<div class="controls">
                                <textarea class="span8" name="category_meta_description" id="category_meta_description">{{ $category->category_meta_description }}</textarea>
                            </div>
                        </div>

                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                    <option value="0" <?php echo ($category->is_active == '0')? 'selected="selected"' : '' ?> >Unpublished</option>
                                    <option value="1" <?php echo (($category->is_active == '1' || $category->is_active == '')? 'selected="selected"' : '') ?>>Published</option>
                                 </select>
                              </div>
                           </div>

                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <a href="{{ route('admin.categories') }}"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
							<input type="hidden" name="id" value="{{{ $category->category_id}}}">

							@foreach($categories as $category)
								<input type="hidden" name="detail_id_{{ $category->language_id }}" value="{{{ $category->category_detail_id}}}">
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
  $('#publication').addClass('active');
  $('#categories').addClass('active');
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
                    category_url: "required",
					attachment:	{
									extension	:'jpe?g|png'
								}
                },
                messages: {
                    category_url: "Please enter url of the category/page. ",

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
