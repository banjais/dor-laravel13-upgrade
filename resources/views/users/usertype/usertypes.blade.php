@extends('layout.admin.masters')
@section('content')
@csrf
    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>


    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        User Types
                        <small>Add, edit, delete user types and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="#">User Type Management</a> <span class="divider">&nbsp;</span>
                        </li>
                        <a href="{{ $abs_site_url }}admin/usertype/add"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i> Add User Type</button></a>
                    </ul>

                </div>
            </div>


            <div id="page">


                <div class="row-fluid">
                    <div class="span12 ">
                        <!-- BEGIN widget widget-->
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>List of Users</h4>

                            </div>
                            <div class="widget-body">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>
                                <div class="scroller" data-height="200px">


                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>User Type Name</th>
                                                <th>Published</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablebody">
                                            {!! $usertypes !!}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END widget widget-->
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div id="myModal1" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel1">Update User Type</h3>

        </div>

        <div class="modal-body">
            <form name="editusertype" id="editusertype" action="{{ $abs_site_url }}admin/usertype/edit" method="post">
                <div class="control-group">
                    <label class="control-label">User Type</label>
                    <div class="controls">
                        <input type="hidden" id="usertypeid" name="id" />
                        <input type="text" name="user_type" id="user_type_edit" class="span8 " />
                    </div>
                </div>
        </div>

        <div class="modal-footer">


            <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i>
                Close</button>
            <div id="msgdisp" class="span5" style="float:left; margin-top:5px;"></div>
        </div>
        </form>

    </div>
@section('footer_js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
        $('#usertypes').addClass('active');
        /* Basic jQuery Validation Form Demo Code
         * Copyright Sam Deering 2012
         * Licence: http://www.jquery4u.com/license/
         */

        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#editusertype").validate({
                        rules: {
                            user_type: "required",

                        },
                        messages: {
                            user_type: "Please enter the title of the user type.",
                        },
                        submitHandler: function(form) {
                            //form.submit();

                            $data = $('#editusertype').serialize();
                            $.ajax({
                                url: $('#editusertype').attr('action'),
                                type: "POST",
                                data: $data,
                                success: function(data) {
                                    //alert(data);
                                    $json = JSON.parse(data);
                                    type = $json.type;
                                    message = $json.message;
                                    if (type == "success") {
                                        $('#msgdisp').empty();
                                        $('#msgdisp').append(
                                            '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                            message + '</div>');
                                        $('#tablebody').empty();
                                        $('#tablebody').append($json.usertypelist);
                                        $('#myModal1').toggle();
                                        $('.modal-backdrop').css('display', 'none');
                                    } else if (type == 'error') {
                                        $('#msgdisp').append(
                                            '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>' +
                                            message + '</div>');
                                    }


                                },
                                error: function(e) {
                                    alert(
                                        "Error occured. Please contact the company developer.");

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
        $(document).on('click', '.edit-user-type', function() {
            $datatosend = "id=" + this.id;
            //alert($datatosend);
            $('#usertypeid').val(this.id);
            $.ajax({
                url: '{{ $abs_site_url }}admin/usertype/get-info-dynamically',
                type: 'POST',
                data: $datatosend,
                success: function(data) {
                    //alert(data);
                    $json = JSON.parse(data);
                    if ($json.type == "success") {
                        $('#user_type_edit').val($json.usertype);
                    }
                }
            });

        });


        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert($datatosend);
            $.ajax({
                url: "{{ $abs_site_url }}admin/usertype/change-status",
                type: "POST",
                data: $datatosend,
                beforeSend: function() {
                    $('#ajax-notifier-background').css('display', 'block');
                },
                success: function(data) {
                    $json = JSON.parse(data);
                    type = $json.type;
                    message = $json.message;
                    if (type == "success") {
                        $('#msgdisp').empty();
                        $('#msgdisp').append(
                            '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                            message + '</div>');
                        $($object).empty();
                        $($object).append($json.icon);

                    }
                },
                complete: function() {
                    $('#ajax-notifier-background').css('display', 'none');
                },
                error: function() {
                    alert("Status couldnot be changed due to of unknown error. Please try again. ");
                }
            });
        });

        $(document).on('click', '.delete-user-type', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/usertype/delete",
                    type: "POST",
                    data: $datatosend,
                    beforeSend: function() {
                        $('#ajax-notifier-background').css('display', 'block');
                    },
                    success: function(data) {
                        $json = JSON.parse(data);
                        type = $json.type;
                        message = $json.message;
                        if (type == "success") {
                            $('#msgdisp').empty();
                            $('#msgdisp').append(
                                '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                message + '</div>');
                            $($object).parents('tr')[0].remove();


                        }
                    },
                    complete: function() {
                        $('#ajax-notifier-background').css('display', 'none');
                    },
                    error: function() {
                        alert(
                            "This record couldnot be deleted due to of unknown error. Please try again. ");
                    }
                });
            }

        })
    </script>
@endsection
@stop
