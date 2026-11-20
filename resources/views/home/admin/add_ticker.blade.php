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
                        Homes
                        <small>Add News Ticker sadas </small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.homes') }}">Home Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Home<span class="divider-last">&nbsp;</span></li>

                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add New Ticker Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">New Ticker Title (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <input type="text" name="ticker_heading_{{ $language->id }}"
                                                    value="{{ old('ticker_heading_' . $language->id) }}" data-required="1"
                                                    class="span8" />
                                            </div>
                                        </div>
                                    @endforeach





                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">New Ticker Description (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20"
                                                    name="ticker_description_{{ $language->id }}">
            {{ old('ticker_description_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

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
                                        <a href="{{ route('admin.homes') }}"> <button type="button"
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
        $('li').removeClass('active');
        $('#content').addClass('active');
        $('#homes').addClass('active');
        <?php foreach($languages as $language) { ?>
        CKEDITOR.replace('ticker_description_<?php echo $language->id; ?>', {
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

            /**
             * Basic jQuery Validation Form Demo Code
             * Copyright Sam Deering 2012
             * Licence: http://www.jquery4u.com/license/
             */

            (function($, W, D) {
                var JQUERY4U = {};

                JQUERY4U.UTIL = {
                    setupFormValidation: function() {
                        //form validation rules
                        $("#addform").validate({
                            rules: {

                                <?php
                                foreach ($languages as $language) {
                                    echo 'ticker_heading_' . $language->id . ': "required",';
                                    echo 'ticker_description_' . $language->id . ': "required",';
                                    //echo 'primary_contact_'.$language->id.':"required",	';
                                }
                                ?>

                            },
                            messages: {

                                <?php
                                foreach ($languages as $language) {
                                    echo 'ticker_heading_' . $language->id . ': "Please enter the ticker title in ' . $language->language_name . '",';
                                    echo 'ticker_description_' . $language->id . ': "Please enter the ticker description in ' . $language->language_name . '",';
                                    //echo 'primary_contact_'.$language->id.':"Please enter the primary contact number in  '.$language->language_name.'",	';
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
