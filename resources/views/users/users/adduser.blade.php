@extends('layout.admin.masters')
@section('content')
    @php
    // dd($usertypelist);
    @endphp

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Users
                        <small>Add user.</small>
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
                        <li>Add User<span class="divider-last">&nbsp;</span></li>



                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->
                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add User Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">User Type</label>
                                        <div class="controls">
                                            <select name="usertype" data-placeholder="Select User Type"
                                                class="chosen-with-diselect span8" tabindex="-1" id="usertype">
                                                {!! $usertypelist !!}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Username *</label>
                                        <div class="controls">
                                            <input type="email" name="username" id="username" data-required="1"
                                                class="span8" value="{{ old('username') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Account Email (OTP / Password Recovery) *</label>
                                        <div class="controls">
                                            <input type="email" name="email_address" id="email_address"
                                                list="office-email-suggestions" data-required="1"
                                                class="span8" value="{{ old('email_address') }}" required />
                                            <datalist id="office-email-suggestions"></datalist>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Password *</label>
                                        <div class="controls">
                                            <input type="password" name="password" id="password" data-required="1"
                                                class="span8 " />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Confirm Password *</label>
                                        <div class="controls">
                                            <input type="password" name="confirm_password" id="confirm_password"
                                                data-required="1" class="span8 " />
                                        </div>
                                    </div>

                                    <div class="control-group" id="office_dropdown">
                                    </div>

                                    <div class="control-group" id="project_dropdown">
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



    </div>


    </div>



    <script type="text/javascript">
        $('document').ready(function() {
            $('li').removeClass('active');
            $('#usersmgmt').addClass('active');
            $('#users').addClass('active');
            $("#office_dropdown").hide();
            $("#project_dropdown").hide();
            $('#usertype').change(function() {
                if (this.value == '3') {
                    $("#office_dropdown").hide();
                    $("#office_dropdown").empty();
                    $("#project_dropdown").empty();
                    $("#project_dropdown").show();
                    var htmlToShow = '<label class="control-label">Project Name</label>' +
                        '<div class="controls">' +
                        '<select name="project_id" data-placeholder="Please Select Project" class="chosen-with-diselect span8" tabindex="-1" id="project-id">' +
                        '<?php echo $projectlist; ?>' +
                        '</select>' +
                        '</div>';
                    $("#project_dropdown").append(htmlToShow);
                } else {
                    $("#project_dropdown").hide();
                    $("#project_dropdown").empty();
                    $("#office_dropdown").empty();
                    $("#office_dropdown").show();
                    var htmlToShow = '<label class="control-label">Office Name</label>' +
                        '<div class="controls">' +
                        '<select name="office_id" data-placeholder="Please Select Office" class="chosen-with-diselect span8" tabindex="-1" id="office-id">' +
                        '<?php echo $officelist; ?>' +
                        '</select>' +
                        '</div>';
                    $("#office_dropdown").append(htmlToShow);
                    updateOfficeEmailSuggestions();
                }
            });

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

            $(document).on('change', '#office-id', updateOfficeEmailSuggestions);
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
                            password: {
                                required: true,
                                minlength: 6
                            },
                            confirm_password: {
                                required: true,
                                minlength: 6,
                                equalTo: '#password'
                            },
                            username: {
                                required: true,
                                email: true
                            },
                            email_address: {
                                required: true,
                                email: true
                            },
                        },
                        messages: {
                            password: {
                                required: "Please enter the password",
                                minlength: "Password must require at least 6 characters."
                            },
                            confirm_password: {
                                required: "Please enter the confirm  password",
                                equalTo: "Confirm password didnot match"
                            },
                            username: {
                                required: "Please enter the username.",
                                email: "Username should be valid email address"
                            },
                            email_address: {
                                required: "Please enter the account email address.",
                                email: "Please enter a valid account email address."
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

@stop
