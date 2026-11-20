@extends('layout.admin.masters')
@section('content')
@php
    // dd($publications);
@endphp
    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
@csrf

    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Publications
                        <small>Add, edit, delete publications and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            Publication Management <span class="divider">&nbsp;</span>
                        </li>
                        @if ($addAccess)
                            <a href="{{ route('admin.publication.add') }}"><button class="btn  btn-primary pull-right"><i
                                        class="icon-plus"></i> Add Publication</button></a>
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
                                <h4><i class="icon-reorder"></i>List of Publications</h4>

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
                                                <th>Publication Heading</th>
                                                @if ($publishAccess)
                                                    <th>Published</th>
                                                @endif
                                                <th>Updated On</th>
                                                @if ($deleteAccess || $editAccess)
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($publications as $index => $publication)
                                                @if ($publication->id)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            @foreach ($publication->details as $detail)
                                                                {{ $detail->publication_heading }}
                                                            @endforeach
                                                        </td>
                                                        @if ($publishAccess)
                                                            <td>
                                                                @if ($publication->is_active == 1)
                                                                    <a href="javascript:void(0)" class="change-status"
                                                                        id="{{ $publication->id }}"><i
                                                                            class="icon-check"></i></a>&nbsp;&nbsp;
                                                                @else
                                                                    <a href="javascript:void(0)" class="change-status"
                                                                        id="{{ $publication->id }}"><i
                                                                            class="icon-check-empty"></i></a>&nbsp;&nbsp;
                                                                @endif
                                                            </td>
                                                        @endif

                                                        <td>{{ date_format(date_create($publication->updated_at), 'M d,Y') }}
                                                        </td>
                                                        @if ($deleteAccess || $editAccess)
                                                            <td>
                                                                @if ($editAccess)
                                                                    <a
                                                                        href="{{ route('admin.publication.edit', $publication->id) }}"><i
                                                                            class="icon-pencil"></i> Edit</a>&nbsp;&nbsp;
                                                                @endif
                                                                @if ($deleteAccess)
                                                                    <a href="javascript:void(0)" class="delete-publication"
                                                                        id="{{ $publication->id }}"><i
                                                                            class="icon-trash"></i>Delete</a>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endif
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
        $('#publication').addClass('active');
        $('#publications').addClass('active');
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            $.ajax({
                url: "{{ $abs_site_url }}admin/publication/change-status",
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


        $(document).on('click', '.delete-publication', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/publication/delete",
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
                            "This record couldnot be deleted due to of unknown error. Please try again. ");
                    }
                });
            }

        })
    </script>
@endsection
@stop
