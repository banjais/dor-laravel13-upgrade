@extends('layout.admin.masters')
@section('content')
    <script>
        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
    </script>

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
                            <a href="#"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="#">User Type Management</a> <span class="divider">&nbsp;</span>
                        </li>
                        <li><a href="#">Add User Type</a><span class="divider-last">&nbsp;</span></li>

                        <a href="{{ $abs_site_url }}admin/user-type/edit/0">
                            <button class="btn  btn-primary top-button pull-right"><i class="icon-edit"></i> Edit User
                                Type</button>

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
                                <h4><i class="icon-reorder"></i>Add User Type Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp1"></div>

                                <form action="{{ $abs_site_url }}admin/usertype/add" class="form-horizontal"
                                    name="addform" id="addform" method="post">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">User Type</label>
                                        <div class="controls">
                                            <input type="text" name="user_type" id="user_type" data-required="1"
                                                class="span8 " />
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="status" data-placeholder="Select the status"
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
                                        <a href="{{ $abs_site_url }}admin/user-types"> <button type="button"
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


    <script src="{{ $rel_site_url }}assets/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="{{ $rel_site_url }}assets/js/jquery.validate.min.js"></script>
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
                    //form validation rules
                    $("#addform").validate({
                        rules: {
                            user_type: "required",


                        },
                        messages: {
                            user_type: "Please enter user type",

                        },
                        submitHandler: function(form) {
                            //form.submit();
                            $data = $('#addform').serialize();
                            $.ajax({
                                url: $('#addform').attr('action'),
                                type: "POST",
                                data: $data,
                                beforeSend: function() {
                                    $('#ajax-notifier-background').css('display',
                                        'block');
                                },
                                success: function(data) {
                                    //alert(data);
                                    $json = JSON.parse(data);
                                    type = $json.type;
                                    message = $json.message;
                                    if (type == "success") {
                                        $('#msgdisp1').empty();
                                        $('#msgdisp1').append(
                                            '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                            message + '</div>');
                                        $('#addform')[0].reset();
                                    } else if (type == 'error') {
                                        $('#msgdisp1').empty();
                                        $('#msgdisp1').append(
                                            '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>' +
                                            message + '</div>');
                                    }


                                },
                                complete: function() {
                                    $('#ajax-notifier-background').css('display',
                                        'none');
                                },
                                error: function(e) {
                                    alert(
                                        "Error occured. Please contact the company developer.");

                                }
                            });


                        }
                    });

                    $("#addAgent").validate({
                        rules: {
                            full_name: "required",
                            email: {
                                //required: true,
                                email: true
                            },
                            /*address: {
                            required: true,

                        },*/
                            contact_number: {
                                required: true
                            },


                        },
                        messages: {
                            full_name: "Please enter your full name of the applicant",
                            //address: "Please enter the address of the applicant",
                            email: "Please enter a valid email address",
                            contact_number: "Please enter the contact number of the applicant",

                            /*last_name: "Please enter your lastname",

                            message: "Please enter your message"*/
                        },
                        submitHandler: function(form) {
                            //form.submit();

                        }
                    });

                    $('#addCompany').validate({

                        rules: {
                            full_name: "required",
                            email: {
                                required: true,
                                email: true
                            },
                            address: {
                                required: true,

                            },
                            contact_number: {
                                required: true
                            },


                        },
                        messages: {
                            full_name: "Please enter your name of the company",
                            address: "Please enter the address of the company",
                            email: "Please enter a valid email address",
                            contact_number: "Please enter the contact number of the applicant",
                            //referenced_by : "Please select the name of the agent"
                            /*last_name: "Please enter your lastname",

                            message: "Please enter your message"*/
                        },
                        submitHandler: function(form) {
                            //form.submit();
                            $data = $('#addCompany').serialize();
                            $.ajax({
                                url: $('#addCompany').attr('action'),
                                type: "POST",
                                data: $data,
                                success: function(data) {
                                    //alert(data);
                                    $json = JSON.parse(data);
                                    type = $json.type;
                                    message = $json.message;
                                    if (type == "success") {
                                        $('#company_id').append("<option value='" +
                                            $json.id + "'>" + $json.name +
                                            "</option>");
                                        $('#company_id').val($json.id).trigger(
                                            "liszt:updated");

                                        $('#msgdisp2').append(
                                            '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                            message + '</div>');
                                        $('#addCompany')[0].reset();
                                    } else if (type == 'error') {

                                        $('#msgdisp2').append(
                                            '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>' +
                                            message + '</div>');
                                    }


                                },
                                error: function(e) {
                                    alert("error occured");

                                }
                            });



                        }


                    });

                }

            }

            //when the dom has loaded setup form validation rules
            $(D).ready(function($) {
                JQUERY4U.UTIL.setupFormValidation();
            });

        })(jQuery, window, document);
    </script>

    <script>
        $(document).ready(function() {
            $('#addapplicant').submit(function(e) {
                e.preventDefault();
                if ($('#company_id').val() != '' && $('#date').val() == "") {
                    $('#date-validation').text(
                        'If you select the compay you must select the date of document forwarded.');
                    return false;
                }


            });

            $('#passport_number').on('focusout', function() {
                datatosend = 'pass=' + $('#passport_number').val();
                //alert(datatosend);
                $.ajax({
                    url: '{{ $abs_site_url }}unique-passport',
                    type: 'POST',
                    data: datatosend,
                    success: function(data) {
                        $json = JSON.parse(data);
                        if ($json.type == 'error') {
                            $('#uniquepassport').empty();
                            $('#uniquepassport').append($json.message);
                        }
                        if ($json.type == 'success') {
                            $('#uniquepassport').empty();

                        }
                    },
                    error: function(e) {
                        alert('error occured');
                    }

                });
            });


        });
    </script>
    <script>
        $(document).ready(function() {
            $('#company_id').on('change', function() {
                if ($('#company_id').val() != '') {
                    $('#company-validation').empty();
                }
                $company_id = 'company_id=' + $('#company_id').val();
                $('#demand_id').empty();
                $.ajax({
                    url: '{{ $abs_site_url }}get-demand-numbers',
                    type: 'POST',
                    data: $company_id,
                    success: function(data) {
                        $json = JSON.parse(data);

                        $('#demand_id').append($json.batchlist);
                        $('#demand_id').trigger("liszt:updated");

                        $('#job_cat').empty();
                        $demand_id = "demand_id=" + $json.batchid;
                        $.ajax({
                            url: '{{ $abs_site_url }}get-job-cateogory-by-demand-id',
                            type: 'POST',
                            data: $demand_id,
                            success: function(data) {
                                $json = JSON.parse(data);
                                $('#job_cat').append($json.joblist);
                                $('#job_cat').val('1').trigger("liszt:updated");
                            }
                        });


                    },
                    error: function(e) {
                        alert('Please contact your software vendor.');
                    }
                });
            });
            //========================================================================================================
            //========================================================================================================
            $('#addAgent').submit(function(e) {
                e.preventDefault();
                $data = $('#addAgent').serialize();
                $.ajax({
                    url: $('#addAgent').attr('action'),
                    type: "POST",
                    data: $data,
                    success: function(data) {
                        //alert(data);
                        $json = JSON.parse(data);
                        type = $json.type;
                        message = $json.message;
                        if (type == "success") {
                            $('#msgdisp').append(
                                '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                message + '</div>');
                            $('#addAgent')[0].reset();
                        } else if (type == 'error') {
                            $('#msgdisp').append(
                                '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>' +
                                message + '</div>');
                        }


                    },
                    error: function(e) {
                        alert("error occured");

                    }
                });


            });


        });
    </script>

@stop
