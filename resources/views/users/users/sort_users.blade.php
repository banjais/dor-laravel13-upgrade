@extends('layout.admin.masters')
@section('content')
    <script>
        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
        $('#user').addClass('active');
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
                        <small>Add, edit, delete users and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ $abs_site_url }}admin/home"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ $abs_site_url }}admin/users">User Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <a href="{{ $abs_site_url }}admin/user/add"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i> Add User</button></a>
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
                                    <div class="row-fluid" id="users">
                                        <ul>
                                            @foreach ($users as $user)
                                                <div class="span3" id="user_{{ $user->id }}">
                                                    <div class="item">
                                                        <?php if(file_exists("uploads/users/".$user->user_attachment) && $user->user_attachment!=''){ ?>
                                                        <img src="{{ asset('uploads/users/' . $user->user_attachment) }}"
                                                            style="width: 200px; height: 150px;" alt="" />
                                                        <?php } else { ?>
                                                        <img src="{{ asset('assets/admin/img/no-image.png') }}"
                                                            style="width: 200px; height: 150px;" alt="" />
                                                        <?php }  ?>
                                                        <span>{{ $user->user_full_name }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- END widget widget-->
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });
            $("#users ul").sortable({
                update: function(event, ui) {
                    var orderUsers = $(this).sortable('serialize');
                    debugger;
                    $.ajax({
                        type: 'POST',
                        url: sitePath + "admin/user/sort_users",
                        data: orderUsers,
                        success: function(response) {
                            var $jsondata = JSON.parse(response);
                            debugger;
                            $("#success-message").html(
                                '<div class="portlet-body"><div class="alert alert-success"><button class="close" data-dismiss="alert"></button>' +
                                $jsondata.message + '</div></div>');

                        },
                        error: function(e) {
                            alert('Server Error');
                        },
                    });
                }
            });
        });
    </script>

@stop
