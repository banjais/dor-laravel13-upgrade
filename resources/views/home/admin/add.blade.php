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
                        <small>Add home.</small>
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
                                <h4><i class="icon-reorder"></i>Add Home Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="{{ !empty($isFlashBoxAdd) ? route('admin.home.store.live') : route('admin.home.store') }}" class="form-horizontal" name="addform"
                                    id="addform" method="post" enctype="multipart/form-data">

                                    @csrf
                                    @if (!empty($isFlashBoxAdd))
                                        <input type="hidden" name="is_flash_box" value="1">
                                    @endif

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Home Heading ( {{ $language->language_name }} )
                                                * </label>
                                            <div class="controls">
                                                <input type="text" name="home_heading_{{ $language->id }}"
                                                    value="{{ old('home_heading_' . $language->id) }}" data-required="1"
                                                    class="span8" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Home Url * </label>
                                        <div class="controls">
                                            <input type="text" readonly="readonly" name="home_url" data-required="1"
                                                class="span8 " value="{{ isset($addurl) ? $addurl : '' }}" />
                                            <p class="general-note"> Note: valid characters are alpha numeric, - (dash)
                                                and _ (underscore)</p>
                                        </div>
                                    </div>

                                    @if (!empty($isFlashBoxAdd))
                                        <div class="widget" style="margin:18px 0;border:1px solid #d8e0e8;border-radius:6px;">
                                            <div class="widget-title" style="background:#f3f6f9;padding:10px 14px;">
                                                <h4 style="margin:0;color:#142D4E;">LIVE UPDATE – Flash Box Details</h4>
                                            </div>
                                            <div class="widget-body" style="padding:15px;">
                                                @include('home.admin.extra_data_details_live')

                                                <div class="control-group">
                                                    <label class="control-label">{{ $the_home->data_3_title }}</label>
                                                    <div class="controls">
                                                        <input type="text" name="data_3" class="span8"
                                                            value="{{ old('data_3', $the_home->data_3) }}">
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">{{ $the_home->data_4_title }}</label>
                                                    <div class="controls">
                                                        <input type="text" name="data_4" class="span8"
                                                            value="{{ old('data_4', $the_home->data_4) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($addurl) && in_array($addurl, [
                                        'dor-spoke-person',
                                        'general-info',
                                        'information-officer',
                                        'grievance-hearing-officer',
                                        'additional-officer'
                                    ], true))
                                        @foreach ($languages as $language)
                                            <div class="control-group">
                                                <label class="control-label">
                                                    Officer Name ({{ $language->language_name }})
                                                </label>
                                                <div class="controls">
                                                    <input type="text"
                                                        name="officer_name_{{ $language->id }}"
                                                        value="{{ old('officer_name_' . $language->id) }}"
                                                        class="span8" />
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Home Description (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20"
                                                    name="home_description_{{ $language->id }}">
                    {{ old('home_description_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Page Title ( {{ $language->language_name }} )
                                            </label>
                                            <div class="controls">
                                                <input type="text" name="home_page_title_{{ $language->id }}"
                                                    id="home_page_title"
                                                    value="{{ old('home_page_title_' . $language->id) }}"
                                                    data-required="1" class="span8" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Meta Tags </label>
                                        <div class="controls">
                                            <textarea class="span8" name="home_meta_tags"
                                                id="home_meta_tags">{{ old('home_meta_tags') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Meta Description </label>
                                        <div class="controls">
                                            <textarea class="span8" name="home_meta_description"
                                                id="home_meta_description">{{ old('home_meta_description') }}</textarea>
                                        </div>
                                    </div>

                                    @if (!empty($isFlashBoxAdd))
                                        {{-- Flash Box uses CKFinder fields only --}}
                                    @elseif(isset($addurl) && in_array($addurl, ['message-from-director-general', 'dor-spoke-person', 'general-info', 'information-officer', 'grievance-hearing-officer', 'additional-officer'], true))
                                        @include('admin.photo_studio.controls', ['photoStudioType' => 'home_officer'])
                                    @else
<div class="control-group">
                                        <label class="control-label">Attachment</label>
                                        <div class="controls">
                                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                                <div class="fileupload-new thumbnail">
                                                    <img src="{{ $rel_site_url }}assets/admin/img/no-image.png" alt="" />
                                                </div>
                                                <div class="fileupload-preview fileupload-exists thumbnail"
                                                    style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                                <div>
                                                    <span class="btn btn-file btn-primary"><span
                                                            class="fileupload-new">Select image</span>
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

                                    @endif

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
@if(isset($addurl) && in_array($addurl, ['message-from-director-general', 'dor-spoke-person', 'general-info', 'information-officer', 'grievance-hearing-officer', 'additional-officer'], true))
    <script src="{{ asset('assets/admin/js/photo-studio.js') }}?v={{ filemtime(public_path('assets/admin/js/photo-studio.js')) }}"></script>
@endif

    <script type="text/javascript">
        $('li').removeClass('active');
        $('#content').addClass('active');
        $('#homes').addClass('active');
        <?php foreach($languages as $language) { ?>
        CKEDITOR.replace('home_description_<?php echo $language->id; ?>', {
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
                                home_url: "required",
                                @if (empty($isFlashBoxAdd))
                                attachment: {
                                    extension: 'jpe?g|png'
                                },
                                @endif
                                <?php
                                foreach ($languages as $language) {
                                    echo 'home_heading_' . $language->id . ': "required",';
                                    echo 'home_description_' . $language->id . ': "required",';
                                    //echo 'primary_contact_'.$language->id.':"required",	';
                                }
                                ?>

                            },
                            messages: {
                                home_url: "Please enter url of the home/page.",
                                @if (empty($isFlashBoxAdd))
                                attachment: {
                                    extension: "Please select appropriate file. Only jpeg/jpg and png format are allowed."
                                },
                                @endif
                                <?php
                                foreach ($languages as $language) {
                                    echo 'home_heading_' . $language->id . ': "Please enter the home heading in ' . $language->language_name . '",';
                                    echo 'home_description_' . $language->id . ': "Please enter the home description in ' . $language->language_name . '",';
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
