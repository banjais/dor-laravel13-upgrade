@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    @endphp

    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('staffs', $languageId) }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('staffs', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div class="table-responsive">
                            <table id="normal_table" class="table table-striped table-bordered">
                                <tr>
                                    <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                    <th></th>
                                    <th>{{ \Helper::getNameOftext('post_name', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('post_label', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('service', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('group', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('sub_group', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('staff_name', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('contact_number', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('contact_email', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('remarks', $languageId) }}</th>
                                </tr>
                                @foreach ($staffs as $index => $staff)
                                    @foreach ($staff->details as $detail)
                                    @endforeach
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($staff->photo)
                                                <img src="{{ asset('uploads/staffs/' . $staff->photo) }}" width="120" />
                                            @else

                                            @endif
                                        </td>
                                        <td>@if ($staff->designation){{ $staff->designation->heading }}@endif</td>
                                        <td>@if ($staff->level){{ $staff->level->heading }}@endif</td>
                                        <td>@if ($staff->service){{ $staff->service->heading }}@endif</td>
                                        <td>@if ($staff->group){{ $staff->group->heading }}@endif</td>
                                        <td>@if ($staff->subgroup){{ $staff->subgroup->heading }}@endif</td>
                                        <td>{{ $detail->full_name }}</td>
                                        <td>{{ $detail->contact_number }}</td>
                                        <td>{{ $staff->email_address }}</td>
                                        <td>{{ $detail->remarks }}</td>
                                    </tr>
                                @endforeach
                            </table>
</div>
                        </div>

                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>
@stop
