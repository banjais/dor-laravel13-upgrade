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
                        Publication
                        <small>Add publication.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.publications') }}">Publication Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Publication<span class="divider-last">&nbsp;</span></li>
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
                                <h4><i class="icon-reorder"></i>Add Publication Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">Category </label>
                                        <div class="controls">
                                            <select name="category_id" data-placeholder="Select the category"
                                                class="chosen-with-diselect span8" tabindex="-1">
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" @if ($category->id == old('category_id')) selected="selected" @endif>
                                                        @foreach ($category->details as $detail) {{ $detail->category_heading }} @endforeach
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Publication Heading (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <input type="text" name="publication_heading_{{ $language->id }}"
                                                    id="publication_heading_{{ $language->id }}" data-required="1"
                                                    class="span8"
                                                    value="{{ old('publication_heading_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Publication Url * </label>
                                        <div class="controls">
                                            <input type="text" name="publication_url" id="publication_url" data-required="1"
                                                class="span8" value="{{ old('publication_url') }}" />
                                            <p class="general-note"> Note: valid characters are alpha numeric, - (dash)
                                            </p>
                                        </div>
                                    </div>

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Publication Description (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20"
                                                    name="publication_description_{{ $language->id }}">
            {{ old('publication_description_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Page Title ( {{ $language->language_name }} )
                                            </label>
                                            <div class="controls">
                                                <input type="text" name="publication_page_title_{{ $language->id }}"
                                                    id="publication_page_title" data-required="1" class="span8"
                                                    value="{{ old('publication_page_title_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Published Date *</label>
                                        <div class="controls">
                                            <input type="text" name="published_date" id="published_date" data-required="1"
                                                class="span8 date-picker" value="{{ old('published_date') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Meta Tags </label>
                                        <div class="controls">
                                            <textarea class="span8" name="publication_meta_tags"
                                                id="publication_meta_tags">{{ old('publication_meta_tags') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Meta Description </label>
                                        <div class="controls">
                                            <textarea class="span8" name="publication_meta_description"
                                                id="publication_meta_description">{{ old('publication_meta_description') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Attachment</label>
                                        <small>File must be: jpg, jpeg, pdf, ppt, pptx<br> Maximum size: 130 MB</small>


                                        <div class="controls">
                                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                                <div class="fileupload-new thumbnail">
                                                    <img src="{{ $rel_site_url }}assets/admin/img/no-image.png" alt="" />
                                                </div>
                                                <div class="fileupload-preview fileupload-exists thumbnail"
                                                    style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                                <div>
                                                    <span class="btn btn-file btn-primary"><span
                                                            class="fileupload-new">Select Attachment</span>
                                                        <span class="fileupload-exists">Change</span>
                                                        <input type="file" class="default" name="attachment"
                                                            id="attchment" /></span>
                                                    <a href="#" class="btn  btn-danger fileupload-exists"
                                                        data-dismiss="fileupload">Remove</a>
                                                    <label class="error" id="backgroundvalid"></label>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="is_active" data-placeholder="Select the status"
                                                class="chosen-with-diselect span8" tabindex="-1" id="status">
                                                <option value="0">Unpublished</option>
                                                <option value="1">Published</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.publications') }}"> <button type="button"
                                                class="btn btn-danger "><i class="icon-remove-sign"></i> Cancel</button></a>
                                    </div>
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
        CKEDITOR.replace('publication_description_<?php echo $language->id; ?>', {
            filebrowserBrowseUrl: sitePath + 'assets/admin/ckfinder/ckfinder.html',
            filebrowserImageBrowseUrl: sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Images',
            filebrowserFlashBrowseUrl: sitePath + 'assets/admin/ckfinder/ckfinder.html?type=Flash',
            filebrowserUploadUrl: sitePath +
                'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            filebrowserImageUploadUrl: sitePath +
                'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            filebrowserFlashUploadUrl: sitePath +
                'assets/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
            filebrowserWindowWidth: '1000',
            filebrowserWindowHeight: '700'
        });
        <?php } ?>

        $(document).ready(function() {
            $('li').removeClass('active');
            $('#publication').addClass('active');
            $('#publications').addClass('active');
            $('#publication_heading_2').blur(function() {
                var heading = $(this).val();
                var url = heading.trim().toLowerCase().replace(/[^a-z1-9]+/gi, '-');
                $("#publication_url").val(url);
            });
        });


        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#addform").validate({
                        rules: {
                            publication_url: "required",
                            published_date: "required",
                            <?php
                            foreach ($languages as $language) {
                                echo 'publication_heading_' . $language->id . ': "required",';
                                echo 'publication_description_' . $language->id . ': "required",';
                            }
                            ?>
                            attachment: {
                                required: true,
                                extension: 'jpe?g|pdf|pptx?',
                                filesize: 130 * 1024 * 1024,
                            }
                        },
                        messages: {
                            publication_url: "Please enter url of the publication.",
                            published_date: "Please enter published date of publication.",
                            <?php
                            foreach ($languages as $language) {
                                echo 'publication_heading_' . $language->id . ': "Please enter the publication heading in ' . $language->language_name . '",';
                                echo 'publication_description_' . $language->id . ': "Please enter the publication description in ' . $language->language_name . '",';
                            }
                            ?>
                            attachment: {
                                required: "Please select attachment.",
                                extension: "Please select JPG, JPEG, PDF, PPT or PPTX files only.",
                                filesize: "Please select attachment of size not greater than 130MB.",
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
