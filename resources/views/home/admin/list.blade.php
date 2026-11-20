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
                        Homes
                        <small>Add, edit, delete homes and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                        </li>
                        <li>
                            Home Management <span class="divider">&nbsp;</span>
                        </li>



                    </ul>
                    @if (!isset($generalId))
                        <a href="{{ $abs_site_url }}admin/home/addgeninfo"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i>Add General Info</button></a>
                    @endif
                    @if (!isset($spokeId))
                        <a href="{{ $abs_site_url }}admin/home/addspoke"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i>Add Spoke Person</button></a>
                    @endif
                    @if (!isset($workingId))
                        <a href="{{ $abs_site_url }}admin/home/addworkarea"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i>Add Working Area</button></a>
                    @endif
                    @if (!isset($aboutId))
                        <a href="{{ $abs_site_url }}admin/home/addaboutus"><button class="btn  btn-primary pull-right"><i
                                    class="icon-plus"></i>Add About Us</button></a>
                    @endif
                    @if (!isset($grievanceId))
                        <a href="{{ $abs_site_url }}admin/home/addgrievance"><button class="btn btn-primary pull-right"><i
                                    class="icon-plus"></i>Add Grievance Hearing Officer</button></a>
                    @endif
                    @if (!isset($additionalOfficerId))
                        <a href="{{ $abs_site_url }}admin/home/addadditionalofficer"><button class="btn btn-primary pull-right"><i class="icon-plus"></i>Add Additional Officer</button></a>
                    @endif

                </div>
            </div>


            <div id="page">

                <div class="row-fluid">
                    <div class="span12 ">
                        <!-- BEGIN widget widget-->
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>List of Homes</h4>

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
                                                <th>Home Group</th>
                                                <th>Home Heading</th>
                                                <th>Updated On</th>
                                                @if ($publishAccess)
                                                <th>Publish</th>
                                                @endif
                                                @if ($editAccess)
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($homes as $index => $home)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{$home->home_identity_name}}</td>
                                                    <td>
                                                        @if ($home->home_url === 'instagram-feed')
                                                            Instagram Feed
                                                        @else
                                                            @foreach ($home->details as $detail)
                                                                {{ $detail->home_heading }}
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ date_format(date_create($home->updated_at), 'M d,Y') }}</td>
                                                    @if ($publishAccess)
                                                        <td>
                                                            @if ($home->is_active == 1)
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $home->id }}"><i
                                                                        class="icon-check"></i></a>&nbsp;&nbsp;
                                                            @else
                                                                <a href="javascript:void(0)" class="change-status"
                                                                    id="{{ $home->id }}"><i
                                                                        class="icon-check-empty"></i></a>&nbsp;&nbsp;
                                                            @endif
                                                        </td>
                                                    @endif
                                                    @if ($deleteAccess || $editAccess)
                                                        <td>
                                                            @if ($editAccess)
                                                                <a href="{{ route('admin.home.edit', $home->id) }}"><i
                                                                        class="icon-pencil"></i> Edit</a>&nbsp;&nbsp;
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
        $('#content').addClass('active');
        $('#homes').addClass('active');

        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            $.ajax({
                url: "{{ $abs_site_url }}admin/home/change-status",
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


        $(document).on('click', '.delete-home', function() {

            $datatosend = "id=" + this.id;
            $object = $(this);
            var con = confirm("Are you sure you want to delete this record.");
            if (con) {
                $.ajax({
                    url: "{{ $abs_site_url }}admin/home/delete",
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
