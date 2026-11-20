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
                        Projects
                        <small>Add project.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.projects') }}">Project Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Project<span class="divider-last">&nbsp;</span></li>

                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add Project Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Project Heading ( {{ $language->language_name }}
                                                ) * </label>
                                            <div class="controls">
                                                <input type="text" name="project_heading_{{ $language->id }}"
                                                    id="project_heading_{{ $language->id }}" data-required="1"
                                                    class="span8"
                                                    value="{{ old('project_heading_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Project Url * </label>
                                        <div class="controls">
                                            <input type="text" name="project_url" id="project_url" data-required="1"
                                                class="span8 " value="{{ old('project_url') }}" />
                                            <p class="general-note"> Note: valid characters are alpha numeric, - (dash)
                                                and _ (underscore)</p>
                                        </div>
                                    </div>


                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Project Description (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20"
                                                    name="project_description_{{ $language->id }}">
            {{ old('project_description_' . $language->id) }}
            </textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Page Title ( {{ $language->language_name }} )
                                            </label>
                                            <div class="controls">
                                                <input type="text" name="project_page_title_{{ $language->id }}"
                                                    id="project_page_title" data-required="1" class="span8"
                                                    value="{{ old('project_page_title_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Meta Tags </label>
                                        <div class="controls">
                                            <textarea class="span8" name="project_meta_tags"
                                                id="project_meta_tags">{{ old('project_meta_tags') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Meta Description </label>
                                        <div class="controls">
                                            <textarea class="span8" name="project_meta_description"
                                                id="project_meta_description">{{ old('project_meta_description') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="span6">
                                            <label class="control-label">Project Class : </label>
                                            <div class="controls">
                                                <input type="text" name="project_class" id="project_class" data-required="1"
                                                    class="gllpLatitude" value="{{ old('project_class') }}" />
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <label class="control-label">Project Ref No: </label>
                                            <div class="controls">
                                                <input type="text" name="project_ref_no" id="project_ref_no"
                                                    data-required="1" class="gllpLongitude"
                                                    value="{{ old('project_ref_no') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="span6">
                                            <label class="control-label">Project Link Code : </label>
                                            <div class="controls">
                                                <input type="text" name="project_link_code" id="project_link_code"
                                                    data-required="1" class="gllpLatitude"
                                                    value="{{ old('project_link_code') }}" />
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <label class="control-label">Project BT: </label>
                                            <div class="controls">
                                                <input type="text" name="project_black_tapped" id="project_black_tapped"
                                                    data-required="1" class="gllpLongitude"
                                                    value="{{ old('project_black_tapped') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="span6">
                                            <label class="control-label">Project GR : </label>
                                            <div class="controls">
                                                <input type="text" name="project_gravel" id="project_gravel"
                                                    data-required="1" class="gllpLatitude"
                                                    value="{{ old('project_gravel') }}" />
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <label class="control-label">Project ER: </label>
                                            <div class="controls">
                                                <input type="text" name="project_earthen" id="project_earthen"
                                                    data-required="1" class="gllpLongitude"
                                                    value="{{ old('project_earthen') }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="span6">
                                            <label class="control-label">Project UC : </label>
                                            <div class="controls">
                                                <input type="text" name="project_under_construction"
                                                    id="project_under_construction" data-required="1" class="gllpLatitude"
                                                    value="{{ old('project_under_construction') }}" />
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <label class="control-label">Project PL: </label>
                                            <div class="controls">
                                                <input type="text" name="project_plan" id="project_plan" data-required="1"
                                                    class="gllpLongitude" value="{{ old('project_plan') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <fieldset class="gllpLatlonPicker">
                                        <div class="control-group">
                                            <label class="control-label">Search Map : </label>
                                            <div class="controls">
                                                <input type="text" class="gllpSearchField">
                                                <input type="button" class="gllpSearchButton" value="search">
                                                <br /><br />
                                                <div class="gllpMap">Google Maps</div>
                                                <br />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="span6">
                                                <label class="control-label">Latitude : </label>
                                                <div class="controls">
                                                    <input type="text" name="project_latitude" id="project_latitude"
                                                        data-required="1" class="gllpLatitude" value="28" />
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <label class="control-label">Longitude: </label>
                                                <div class="controls">
                                                    <input type="text" name="project_longitude" id="project_longitude"
                                                        data-required="1" class="gllpLongitude" value="84" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Zoom: </label>
                                            <div class="controls">
                                                <input type="text" data-required="1" class="gllpZoom"
                                                    name="project_zoom" value="7" />
                                                <input type="button" class="gllpUpdateButton" value="update map">
                                            </div>
                                        </div>
                                    </fieldset> --}}
                                    <div class="control-group">
                                        <label class="control-label">Google map embed code (with iframe): </label>
                                        <div class="controls">
                                            <textarea style="width:100%" rows="5" type="text" name="google_map_embed" id="google_map_embed" data-required="1"
                                                class="gllpZoom" value="7"></textarea>
                                        </div>
                                    </div>

                                    <br />

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
                                        <a href="{{ route('admin.projects') }}"> <button type="button"
                                                class="btn btn-danger "><i class="icon-remove-sign"></i>
                                                Cancel</button></a>
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
        $(document).ready(function() {
            $('li').removeClass('active');
            $('#content').addClass('active');
            $('#projects').addClass('active');
            $("#project_heading_2").blur(function() {
                var heading = $(this).val();
                var url = heading.toLowerCase().trim().replace(/[^a-z0-9]+/gi, '-');
                $("#project_url").val(url);
            });
            <?php foreach($languages as $language) { ?>
            CKEDITOR.replace('project_description_<?php echo $language->id; ?>', {
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
        });

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
                            project_url: "required",
                            <?php
                            foreach ($languages as $language) {
                                echo 'project_heading_' . $language->id . ': "required",';
                                echo 'project_description_' . $language->id . ': "required",';
                                //echo 'primary_contact_'.$language->id.':"required",	';
                            }
                            ?>
                            attachment: {
                                extension: 'jpe?g|png'
                            }
                        },
                        messages: {
                            project_url: "Please enter url of the project/page.",
                            <?php
                            foreach ($languages as $language) {
                                echo 'project_heading_' . $language->id . ': "Please enter the project heading in ' . $language->language_name . '",';
                                echo 'project_description_' . $language->id . ': "Please enter the project description in ' . $language->language_name . '",';
                                //echo 'primary_contact_'.$language->id.':"Please enter the primary contact number in  '.$language->language_name.'",	';
                            }
                            ?>

                            attachment: {
                                extension: "Please select appropriate file. Only jpeg/jpg and png format are allowed."
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

    // <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

    // <!-- CSS and JS for our code -->
    // <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.css') }}" />
    // <script src="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.js') }}"></script>
@endsection

@stop
