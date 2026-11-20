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
                        Office Chiefs
                        <small>Add, edit, delete office chiefs and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="#">Office Chiefs Management</a> <span class="divider">&nbsp;</span>
                        </li>
                        @if ($addAccess)
                            <a href="{{ route('admin.office_chiefs.add') }}"><button class="btn  btn-primary pull-right"><i
                                        class="icon-plus"></i> Add Office Chief</button></a>
                        @endif
                        <a href="{{ route('admin.office_chiefs.sortorder') }}"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i> Sort Office Chiefs</button></a>
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


                                    <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>Photo</th>
                                                <th>Full Name</th>
                                                <th>Chief Type</th>
                                                <th>Service Date (AD)</th>
                                                <th>Service Date (BS)</th>
                                                @if ($publishAccess)
                                                    <th>Published</th>
                                                @endif
                                                @if ($editAccess || $deleteAccess)
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($staffs as $index => $staff)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if ($staff->photo)
                                                        <img src="{{asset('uploads/office_chiefs/'.$staff->photo)}}" width="120"/>
                                                        @else
                                                        <img src="{{asset('assets/admin/img/no-image.png')}}" width="120" />
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @foreach ($staff->details as $detail)
                                                            {{ $detail->full_name }}
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($staff->chief_type){{ $staff->chief_type }}@endif
                                                    </td>
                                                    <td>
                                                        {{$staff->date_ad}}
                                                    </td>
                                                    <td>
                                                        {{$staff->date_bs}}
                                                    </td>
                                                    @if ($publishAccess)
                                                        <td>
                                                            <a href="javascript:;" style="font-size:1.4em;"
                                                                class="change-status" id="{{ $staff->id }}">
                                                                <span>@if ($staff->is_active == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span>
                                                            </a>
                                                        </td>
                                                    @endif
                                                    @if ($editAccess || $deleteAccess)
                                                        <td>
                                                            @if ($editAccess)
                                                                <a href="{{ route('admin.office_chiefs.edit', $staff->id) }}"
                                                                    id="{{ $staff->id }}"><i class="icon-edit"></i>
                                                                    Edit</a>&nbsp;&nbsp;
                                                            @endif
                                                            @if ($deleteAccess)
                                                                <a href="javascript:;" class="delete-staff"
                                                                    id="{{ $staff->id }}"><i class="icon-trash"></i>
                                                                    Delete</a>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
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

@section('footer_js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $('li').removeClass('active');
        $('#staffsmgmt').addClass('active');
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            $.ajax({
                url: "{{ $abs_site_url }}admin/office_chiefs/change-status",
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

        $(document).on('click', '.delete-staff', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/office_chiefs/delete",
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
                            var oTable = $('#sample_editable_1').dataTable();
                            var nRow = $($object).parents('tr')[0];
                            oTable.fnDeleteRow(nRow);

                        }
                    },
                    complete: function() {
                        $('#ajax-notifier-background').css('display', 'none');
                    },
                    error: function() {
                        alert(
                            "This record couldnot be deleted due to of unknown error. Please try again. "
                            );
                    }
                });
            }

        })
    </script>
@endsection
@stop
