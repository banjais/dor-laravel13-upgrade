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
                        External Tools
                        <small>Add, edit, delete external tools</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="#">External Tools Management</a> <span class="divider">&nbsp;</span>
                        </li>
                        @if ($addAccess)
                            <a href="{{ route('admin.tools.add') }}"><button class="btn  btn-primary pull-right"><i
                                        class="icon-plus"></i> Add Tool</button></a>
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
                                <h4><i class="icon-reorder"></i>List of External Tools</h4>

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
                                                <th>Display Text</th>
                                                <th>Hover Text</th>
                                                <th>Icon</th>
                                                @if ($publishAccess)
                                                    <th>Published</th>
                                                @endif
                                                @if ($editAccess || $deleteAccess)
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tools as $index => $tool)
                                                <tr class="">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $tool->display_text }}</td>
                                                    <td>{{ $tool->hover_text }}</td>
                                                    <td>{{ $tool->icon }}</td>
                                                    @if ($publishAccess)
                                                        <td>
                                                            <a href="javascript:;" style="font-size:1.4em;"
                                                                class="change-status" id="{{ $tool->id }}">
                                                                <span>@if ($tool->is_active == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span>
                                                            </a>
                                                        </td>
                                                    @endif
                                                    @if ($editAccess || $deleteAccess)
                                                        <td>
                                                            @if ($editAccess)
                                                                <a href="{{ route('admin.tools.edit', $tool->id) }}"
                                                                    id="{{ $tool->id }}"><i class="icon-edit"></i>
                                                                    Edit</a>&nbsp;&nbsp;
                                                            @endif
                                                            @if ($deleteAccess)
                                                                <a href="javascript:;" class="delete-tool"
                                                                    id="{{ $tool->id }}"><i class="icon-trash"></i>
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
        $('#misc').addClass('active');
        $('#tools').addClass('active');
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            $.ajax({
                url: "{{ route('admin.tools.change.status') }}",
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

        $(document).on('click', '.delete-tool', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ route('admin.tools.delete') }}",
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
