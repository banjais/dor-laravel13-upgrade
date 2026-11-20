@extends('layout.admin.masters')
@section('content')
    <script>
        $('li').removeClass('active');
        $('#staffsmgmt').addClass('active');
        $('#staff').addClass('active');
    </script>
    @csrf
    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>


    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Office Chefs
                        <small>Add, edit, delete office chiefs and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.office_chiefs') }}">Office Chief Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        @if ($addAccess)
                            <a href="{{ route('admin.office_chiefs.add') }}"><button class="btn  btn-primary pull-right"><i
                                        class="icon-plus"></i> Add Office Chief</button></a>
                        @endif
                    </ul>

                </div>
            </div>


            <div id="page">

                <div class="row-fluid">
                    <div class="span12 ">
                        <!-- BEGIN widget widget-->
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>List of Office Chiefs</h4>

                            </div>
                            <div class="widget-body">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>
                                <div class="scroller" data-height="200px">
                                    <div class="row-fluid" id="staffs">
                                        <ul>
                                            @foreach ($staffs as $index => $staff)
                                                <div class="span3" id="staff_{{ $staff->id }}">
                                                    <div class="item">
                                                        <?php if(file_exists("uploads/office_chiefs/".$staff->photo) && $staff->photo!=''){ ?>
                                                        <img src="{{ asset('uploads/office_chiefs/' . $staff->photo) }}"
                                                            style="width: 200px; height: 150px;" alt="" />
                                                        <?php } else { ?>
                                                        <img src="{{ asset('assets/admin/img/profile.jpg') }}"
                                                            style="width: 200px; height: 150px;" alt="" />
                                                        <?php }  ?>
                                                        @foreach ($staff->details as $detail)
                                                            <span>{{ $detail->full_name }}</span>
                                                        @endforeach
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $(document).ready(function() {
            $("#staffs ul").sortable({
                update: function(event, ui) {
                    var orderStaffs = $(this).sortable('serialize');
                    debugger;
                    $.ajax({
                        type: 'POST',
                        url: sitePath + "admin/office_chiefs/sort_office_chiefs",
                        data: orderStaffs,
                        success: function(response) {
                            var $jsondata = JSON.parse(response);
                            debugger;
                            $("#msgdisp").html(
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
