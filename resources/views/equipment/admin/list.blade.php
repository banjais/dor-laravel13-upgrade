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
                        Equipments
                        <small>Add, edit, delete equipments and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            Equipment Management <span class="divider">&nbsp;</span>
                        </li>
                        @if ($addAccess)
                            <a href="{{ route('admin.equipment.add') }}"><button class="btn  btn-primary pull-right"><i
                                        class="icon-plus"></i> Add Equipment</button></a>
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
                                <h4><i class="icon-reorder"></i>List of Equipments</h4>
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
                                                <th>Equipment Name</th>
                                                @if ($publishAccess)
                                                    <th>Published</th>
                                                @endif
                                                <th>Total</th>
                                                <th>Updated On</th>
                                                @if ($deleteAccess || $editAccess)
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- */ $count = 1 /* --}}
                                            @foreach ($equipments as $key => $equipment)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $equipment->equipment_name }}</td>

                                                    @if ($publishAccess)
                                                        <td>
                                                            @if ($equipment->is_active == 1)
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $equipment->id }}"><i
                                                                        class="icon-check"></i></a>&nbsp;&nbsp;
                                                            @else
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $equipment->id }}"><i
                                                                        class="icon-check-empty"></i></a>&nbsp;&nbsp;
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>{{ $equipment->total_equipments }}</td>
                                                    <td>{{ $equipment->updated_on }}</td>
                                                    @if ($deleteAccess || $editAccess)
                                                        <td>
                                                            @if ($editAccess)
                                                                <a href="equipment/edit/{{ $equipment->id }}"><i
                                                                        class="icon-pencil"></i> Edit</a>&nbsp;&nbsp;
                                                            @endif
                                                            @if ($deleteAccess)
                                                                <a href="javascript:void(0)" class="delete-equipment"
                                                                    id="{{ $equipment->id }}"><i
                                                                        class="icon-trash"></i>Delete</a>
                                                            @endif
                                                            <a href="javascript:void(0)" class="add-equipment-log"
                                                                id="{{ $equipment->id }}"><i
                                                                    class="icon-pencil"></i>Update Log</a>
                                                            <a href="equipment/view-detail/{{ $equipment->id }}"><i
                                                                    class="icon-trash"></i>View Detail</a>
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


    <div id="myModal" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel1">Add Available Equipment Log</h3>
        </div>
        <form name="addEquipmentCount" id="addEquipmentCount" action="{{ $abs_site_url }}admin/equipment/add-log"
            method="post">
            <div class="modal-body">
                <div class="control-group">
                    <label class="control-label">Equipment Log</label>
                    <div class="controls">
                        <input type="text" name="equipment_log" id="equipment_log" class="span5" type="number" />
                        <input type="hidden" name="equipment_id" id="equipment_id" class="span5" value="" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
                <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i>
                    Close</button>
                <div id="msgdisp1" class="span5" style="float:left; margin-top:5px;"></div>
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
        $('#misc').addClass('active');
        $('#equipments').addClass('active');
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            $.ajax({
                url: "{{ $abs_site_url }}admin/equipment/change-status",
                type: "POST",
                data: $datatosend,
                beforeSend: function() {
                    $('#ajax-notifier-background').css('display', 'block');
                },
                success: function(response) {
                    $json = JSON.parse(response);
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


        $(document).on('click', '.delete-equipment', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/equipment/delete",
                    type: "POST",
                    data: $datatosend,
                    beforeSend: function() {
                        $('#ajax-notifier-background').css('display', 'block');
                    },
                    success: function(response) {
                        $json = JSON.parse(response);
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

        $(document).on('click', '.add-equipment-log', function() {
            $equipment_id = $(this).attr('id');
            $object = $(this);
            $data = 'equipment_id=' + $equipment_id;
            $.ajax({
                data: $data,
                type: 'POST',
                url: "{{ $abs_site_url }}admin/equipment/find-log",
                success: function(data) {
                    $json = JSON.parse(data);
                    $("#myModal").modal('show');
                    $("#equipment_id").val($equipment_id);
                    $("#equipment_log").val($json.equipment_log);
                }
            });

        });

        $("#addEquipmentCount").validate({
            rules: {
                equipment_log: "required",
            },
            messages: {
                equipment_log: "Please enter the equipment log.",
            },
            submitHandler: function(form) {
                //form.submit();
                $data = $('#addEquipmentCount').serialize();
                $.ajax({
                    url: $('#addEquipmentCount').attr('action'),
                    type: "POST",
                    data: $data,
                    success: function(data) {
                        $json = JSON.parse(data);
                        type = $json.type;
                        message = $json.message;
                        if (type == "success") {
                            $('#msgdisp1').append(
                                '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                                message + '</div>');
                            $('#addEquipmentCount')[0].reset();
                            $("#equipment_log").val($json.equipment_log.equipment_log);
                            window.location.reload();
                        } else if (type == 'error') {
                            $('#msgdisp1').append(
                                '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button><strong>Error! </strong>' +
                                message + '</div>');
                        }
                    },
                    error: function(e) {
                        alert("Error occured. Please contact the company developer.");
                    }
                });


            }
        });
    </script>
@endsection
@stop
