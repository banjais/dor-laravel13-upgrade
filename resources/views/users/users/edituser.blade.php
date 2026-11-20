@extends('layout.admin.masters')
@section('content')
    <style>
        #sample_1_wrapper .row-fluid div {
            position: relative;
            width: inherit;
        }

        #sample_1_wrapper .row-fluid div #sample_1_filter {
            position: relative;
            width: inherit;
        }


        #sample_1_wrapper .row-fluid div #sample_1_filter label {

            position: relative;
            width: inherit;
        }

        #sample_1_wrapper .row-fluid div #sample_1_filter label input {
            display: inline-block;
            width: 80%;
            margin-left: 3%;
        }

        #sample_1_wrapper .row-fluid div .dataTables_paginate.paging_bootstrap.pagination {
            float: right !important;
            width: auto;
        }

        #sample_1_wrapper .row-fluid div #sample_1_info {
            text-align: right;
            margin: 0;
            padding-top: 5px;
        }

    </style>

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>

    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Users
                        <small>Edit Users.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}">User Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li><a href="#">Edit User</a><span class="divider-last">&nbsp;</span></li>
                        @if ($addAccess)
                            <a href="{{ $abs_site_url }}admin/user/add">
                                <button class="btn  btn-primary top-button pull-right "><i class="icon-plus"></i> Add
                                    User</button>
                            </a>
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
                                <h4><i class="icon-reorder"></i> Edit users Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="edit" id="editform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">User Type</label>
                                        <div class="controls">
                                            <select name="usertype" data-placeholder="Select User Type"
                                                class="chosen-with-diselect span8" tabindex="-1" id="usertype">
                                                @foreach ($usertypes as $usertype)
                                                    <option value="{{ $usertype->id }}" @if ($usertype->id == $user->user_type_id) selected="selected" @endif @if ($user->user_type_id == 3) disabled="disabled" @endif>
                                                        {{ $usertype->user_type_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Username *</label>
                                        <div class="controls">
                                            <input type="email" name="username" id="username" data-required="1"
                                                class="span8" value="{{ $user->username }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Account Email (OTP / Password Recovery) *</label>
                                        <div class="controls">
                                            <input type="email" name="email_address" id="email_address"
                                                list="office-email-suggestions" class="span8"
                                                value="{{ old('email_address', $user->email_address) }}" required />
                                            <datalist id="office-email-suggestions"></datalist>
                                        </div>
                                    </div>

                                    @if ($user->user_type_id != 3)
                                        <div class="control-group">
                                            <label class="control-label">Office Name</label>
                                            <div class="controls">
                                                <select name="office_id" data-placeholder="Please Select Office"
                                                    class="chosen-with-diselect span8" tabindex="-1" id="office-id">
                                                    @foreach ($offices as $office)
                                                        <option value="{{ $office->id }}"
                                                            data-primary-email="{{ $office->office_email_address ?? '' }}"
                                                            data-alternative-email="{{ $office->office_alternative_email ?? '' }}"
                                                            @if ($office->id == $user->office_id) selected="selected" @endif>
                                                            {{ $office->office_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="control-group">
                                            <label class="control-label">Project Name</label>
                                            <div class="controls">
                                                <select name="project_id" data-placeholder="Please Select Project"
                                                    class="chosen-with-diselect span8" tabindex="-1" id="project-id">
                                                    @foreach ($projects as $project)
                                                        <option value="{{ $project->id }}" @if ($project->id == $user->project_id) selected="selected" @endif>
                                                            {{ $project->project_heading }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="is_active" data-placeholder="Select the status"
                                                class="chosen-with-diselect span8" tabindex="-1" id="status">
                                                <option value="0" @if ($user->is_active == 0) selected="selected" @endif>Unpublished</option>
                                                <option value="1" @if ($user->is_active == 1) selected="selected" @endif>Published</option>
                                            </select>

                                        </div>

                                    </div>
                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>

                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.users') }}"> <button type="button"
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
        /**
         * Basic jQuery Validation Form Demo Code
         * Copyright Sam Deering 2012
         * Licence: http://www.jquery4u.com/license/
         */
        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
        $('#users').addClass('active');

        (function($, W, D) {
            var JQUERY4U = {};
            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#editform").validate({
                        rules: {
                            username: {
                                required: true,
                                email: true
                            },
                            email_address: {
                                required: true,
                                email: true
                            }
                        },
                        messages: {
                            username: {
                                required: "Please enter a valid username email address",
                                email: "Please enter a valid email address"
                            },
                            email_address: {
                                required: "Please enter the account email address",
                                email: "Please enter a valid account email address"
                            }
                        },
                    });
                }
            }
            //when the dom has loaded setup form validation rules
            function updateOfficeEmailSuggestions() {
                var $selectedOffice = $('#office-id option:selected');
                var $suggestions = $('#office-email-suggestions');

                $suggestions.empty();

                if (!$selectedOffice.length) {
                    return;
                }

                [
                    $selectedOffice.attr('data-primary-email'),
                    $selectedOffice.attr('data-alternative-email')
                ].forEach(function(email) {
                    if (email && email.trim()) {
                        $('<option>').attr('value', email.trim()).appendTo($suggestions);
                    }
                });
            }

            $(D).ready(function($) {
                JQUERY4U.UTIL.setupFormValidation();
                updateOfficeEmailSuggestions();
            });

            $(D).on('change', '#office-id', updateOfficeEmailSuggestions);

        })(jQuery, window, document);
    </script>
@endsection
@stop
