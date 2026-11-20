@extends('layout.admin.masters')
@section('content')
    @php
    $user = Auth::user();
    $roleId = (int) ($user->user_type_id ?? 0);
    $is_restricted_admin = in_array($roleId, [12, 13], true);

    @endphp

    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">
                    <h3 class="page-title">
                        Dashboard
                        <small>Reports, alerts, notifications and more</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ $abs_site_url . 'admin/home' }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="">Dashboard</a> <span class="divider">&nbsp;</span>
                        </li>

                    </ul>

                </div>
            </div>

            <div id="page">
                <div class="row-fluid circle-state-overview">
                    @if (!$is_restricted_admin)
                        <div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2">
                            <div class="circle-wrap">
                                <div class="stats-circle blue-color">
                                    <i><img src="{{ $abs_site_url }}assets/admin/img/candidates.png" /></i>
                                </div>
                                <p>
                                    <strong>+{{ $content_count }}</strong>
                                    Contents
                                </p>
                            </div>
                        </div>

                        <div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2">
                            <div class="circle-wrap">
                                <div class="stats-circle blue-color">
                                    <i><img src="{{ $abs_site_url }}assets/admin/img/agents.png" /></i>
                                </div>
                                <p>
                                    <strong>+{{ $project_count }}</strong>
                                    Projects
                                </p>
                            </div>
                        </div>

                        <div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2">
                            <div class="circle-wrap">
                                <div class="stats-circle blue-color">
                                    <i><img src="{{ $abs_site_url }}assets/admin/img/demands.png" /></i>
                                </div>
                                <p>
                                    <strong>+{{ $staff_count }}</strong>
                                    Staffs
                                </p>
                            </div>
                        </div>

                        <div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2">
                            <div class="circle-wrap">
                                <div class="stats-circle blue-color">
                                    <i><img src="{{ $abs_site_url }}assets/admin/img/reports.png" /></i>
                                </div>
                                <p>
                                    <strong>+{{ $notice_count }}</strong>
                                    Notices
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="span2 responsive clearfix" data-tablet="span3" data-desktop="span2">
                        <a href="/admin/circulars">
                            <div class="circle-wrap">
                                <div class="stats-circle blue-color">
                                    <i><img src="{{ $abs_site_url }}assets/admin/img/reports.png" /></i>
                                </div>
                                <p>
                                    <strong>+{{ $circular_count }}</strong>
                                    Private Circulars
                                </p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </div>
@stop
