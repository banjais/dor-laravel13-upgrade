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
                        Login Credentails Requests
                        <small>manage requests and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            Login Credentails Requests<span class="divider">&nbsp;</span>
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
                                <h4><i class="icon-reorder"></i>List of Staffs</h4>

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
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Designation</th>
                                                <th>Office Name</th>
                                                <th>Office Address</th>
                                                <th>Office Phone</th>
                                                <th>Email Login</th>
                                                <th>Web Login</th>
                                                <th>Circular Login</th>
                                                <th>Completed</th>
                                                <th>Created</th>
                                                <th>Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($loginrequests as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->full_name }}</td>
                                                    <td>{{ $item->email }}</td>
                                                    <td>
                                                        {{ $item->designation }}
                                                    </td>
                                                    <td>{{ $item->office_name }}</td>
                                                    <td>{{ $item->office_address }}</td>
                                                    <td>{{ $item->office_phone }}</td>
                                                    <td><span>@if ($item->is_email_login == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span></td>
                                                    <td><span>@if ($item->is_web_login == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span></td>
                                                    <td><span>@if ($item->is_circular_login == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span></td>
                                                    <td>
                                                        <a href="javascript:;" style="font-size:1.4em;"
                                                            class="change-status" id="{{ $item->id }}">
                                                            <span>@if ($item->is_complete == '1') <i class="icon-check"></i> @else <i class=" icon-check-empty"></i> @endif</span>
                                                        </a>
                                                    </td>
                                                    <td>{{ dateFormat($item->created_at) }}</td>
                                                    <td>{{ dateFormat($item->updated_at) }}</td>
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
        $('#loginrequest').addClass('active');
        $(document).on('click', '.change-status', function() {
            $datatosend = "id=" + this.id;
            $object = $(this);
            //alert('Hello');
            $.ajax({
                url: "{{ $abs_site_url }}admin/login-credentials/change-status",
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
    </script>

@endsection
@stop
