@extends('layout.admin.masters')
@section('content')

    <script>
        $('li').removeClass('active');
        $('#staffsmgmt').addClass('active');
        $('#emailrequest').addClass('active');
    </script>
    <link rel="stylesheet" type="text/css"
        href="{{ $rel_site_url }}assets/admin/bootstrap-datepicker/css/datepicker.css" />

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Email Request
                        <small>form.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Email Request<span class="divider-last">&nbsp;</span></li>
                    </ul>

                </div>
            </div>


            <div id="page">
                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Email Request Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp1">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="emailRequestForm" id="emailRequestForm"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">Name *</label>
                                        <div class="controls">
                                            <input type="text" name="name" id="name" data-required="1"
                                                class="span8 " value="{{ old('name') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">
                                            Designation *
                                        </label>
                                        <div class="controls">
                                            <select name="designation_id" data-placeholder="Select the designation"
                                                class="chosen-with-diselect span8" tabindex="-1" id="designation">
                                                <option value="">Select Designation</option>
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}" @if (old('designation_id') == $designation->id) selected="selected" @endif>
                                                        {{ $designation->heading }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Contact Number</label>
                                        <div class="controls">
                                            <input type="text" name="contact_number" id="contact_number" data-required="1"
                                                class="span8 " value="{{ old('contact_number') }}" />
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Prefered Username *</label>
                                        <div class="controls">
                                            <input type="text" name="username" id="username" data-required="1"
                                                class="span8 " value="{{ old('username') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Email Address</label>
                                        <div class="controls">
                                            <input type="email" name="email_address" id="email_address" data-required="1"
                                                class="span8 " value="{{ old('email_address') }}" />
                                        </div>
                                    </div>

                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ URL::previous() }}"> <button type="button" class="btn btn-danger "><i
                                                    class="icon-remove-sign"></i> Cancel</button></a>
                                    </div>
                                </form>

                            </div>
                        </div>

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

        (function($, W, D) {
            var JQUERY4U = {};
            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    $("#emailRequestForm").validate({
                        rules: {
                            name: "required",
                            designation_id: "required",
                            username: "required",
                        },
                        messages: {
                            name: "Please enter full name.",
                            designation_id: "Please select designation.",
                            username: "Please enter prefered username.",
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
