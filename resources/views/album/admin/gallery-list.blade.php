@extends('layout.admin.masters')
@section('content')
    <script>
        $('li').removeClass('active');
        $('#galleries').addClass('active');
        $('#albums').addClass('active');
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
                        Galleries
                        <small>Add, edit, delete galleries and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.albums') }}">Album Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            Galleries <span class="divider">&nbsp;</span>
                        </li>

                    </ul>

                </div>
            </div>


            <div id="page">

                <div class="row-fluid">
                    <div class="span12 ">
                        <!-- BEGIN widget widget-->
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>List of Galleries</h4>

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
                                                <th>Gallery Title</th>
                                                <th>Gallery Image</th>
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
                                            @foreach ($galleries as $index => $gallery)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @foreach ($gallery->details as $detail)
                                                            {{ $detail->gallery_title }}
                                                        @endforeach
                                                    </td>
                                                    @if (file_exists('uploads/gallery/' . $gallery->gallery_attachment) && $gallery->gallery_attachment != '')
                                                        <td><img src="../../../uploads/gallery/{{ $gallery->gallery_attachment }}"
                                                                style="width: 200px; height: 120px;"></td>
                                                    @else
                                                        <td><img src="../../../uploads/no-image.png"
                                                                style="width: 200px; height: 120px;"></td>
                                                    @endif

                                                    @if ($publishAccess)
                                                        <td>
                                                            @if ($gallery->is_active == 1)
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $gallery->id }}"><i
                                                                        class="icon-check"></i></a>&nbsp;&nbsp;
                                                            @else
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $gallery->id }}"><i
                                                                        class="icon-check-empty"></i></a>&nbsp;&nbsp;
                                                            @endif
                                                        </td>
                                                    @endif

                                                    <td>{{ date_format(date_create($gallery->updated_at), 'M d,Y') }}</td>
                                                    @if ($deleteAccess || $editAccess)
                                                        <td>
                                                            @if ($editAccess)
                                                                <a href="{{ route('admin.gallery.edit', $gallery->id) }}"><i
                                                                        class="icon-pencil"></i> Edit</a>&nbsp;&nbsp;
                                                            @endif
                                                            @if ($deleteAccess)
                                                                <a href="javascript:void(0)" class="delete-gallery"
                                                                    id="{{ $gallery->id }}"><i
                                                                        class="icon-trash"></i>Delete</a>
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

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            $.ajax({
                url: "{{ $abs_site_url }}admin/gallery/change-status",
                type: "POST",
                dataType: "json",
                data: $datatosend,
                beforeSend: function() {
                    $('#ajax-notifier-background').css('display', 'block');
                },
                success: function(response) {
                    $json = response;
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


        $(document).on('click', '.delete-gallery', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/gallery/delete",
                    type: "POST",
                    dataType: "json",
                    data: $datatosend,
                    beforeSend: function() {
                        $('#ajax-notifier-background').css('display', 'block');
                    },
                    success: function(response) {
                        $json = response;
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
                    error: function(xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : "Request failed (HTTP " + xhr.status + "). Please check your access or server logs.";
                        alert("Gallery could not be deleted: " + message);
                    }
                });
            }

        })
    </script>
@stop
