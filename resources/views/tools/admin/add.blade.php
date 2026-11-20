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
                        External Tools
                        <small>Add tool</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.tools') }}">External Tools Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add External Tool<span class="divider-last">&nbsp;</span></li>
                    </ul>
                </div>
            </div>
            <div id="page">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add External Tool Form</h4>
                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>
                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">Display Text *</label>
                                        <div class="controls">
                                            <input type="text" name="display_text" id="display_text" data-required="1"
                                                class="span8" value="{{ old('display_text') }}" />
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Hover Text *</label>
                                        <div class="controls">
                                            <input type="text" name="hover_text" id="hover_text" data-required="1"
                                                class="span8" value="{{ old('hover_text') }}" />
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Tool Url *</label>
                                        <div class="controls">
                                            <input type="text" name="tool_url" id="tool_url" data-required="1"
                                                class="span8" value="{{ old('tool_url') }}" />
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Icon *</label>
                                        <div class="controls">
                                            <input type="text" name="tool_icon" id="tool_icon" data-required="1"
                                                class="span8" value="{{ old('tool_icon') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="is_active" data-placeholder="Select the status"
                                                class="chosen-with-diselect span8" tabindex="-1" id="status">
                                                <option value="0">Unpublished</option>
                                                <option value="1" selected="selected">Published</option>

                                            </select>

                                        </div>

                                    </div>
                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.tools') }}"> <button type="button"
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

    <script type="text/javascript">
        $('document').ready(function() {
            $('li').removeClass('active');
            $('#misc').addClass('active');
            $('#tools').addClass('active');
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
                    $("#addform").validate({
                        rules: {
                            display_text: {
                                required: true,
                                maxlength: 50
                            },
                            hover_text: {
                                required: true,
                                maxlength: 255
                            },
                            tool_url: {
                                url: true,
                                required: true,
                                maxlength: 255
                            },
                            tool_icon: {
                                required: true,
                                maxlength: 50
                            },
                        },
                        messages: {
                            display_text: {
                                required: "Please write display text",
                                maxlength: "Display text cannot be more than 50 characters."
                            },
                            hover_text: {
                                required: "Please write hover text",
                                maxlength: "Hover text cannot be more than 255 characters."
                            },
                            tool_url: {
                                url: 'Please write valid url',
                                required: "Please specify external tool url",
                                maxlength: "Hover text cannot be more than 255 characters."
                            },
                            tool_icon: {
                                required: "Please specify icon",
                                maxlength: "Icon cannot be more than 50 characters."
                            },
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
