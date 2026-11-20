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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('office_chiefs', $languageId) }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('office_chiefs', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                            <p>{{ \Helper::getNameOftext('office_chiefs_list', $languageId) }}</p>
                            <div class="table-responsive">
                            <table id="normal_table" class="table table-striped table-bordered">
                                <tr>
                                    <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                    <th></th>
                                    <th>{{ \Helper::getNameOftext('office_chiefs_engineer', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('office_chiefs_date_ad', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('office_chiefs_date_bs', $languageId) }}</th>
                                </tr>
                                @foreach ($staffs as $index => $staff)
                                    @foreach ($staff->details as $detail)
                                    @endforeach
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($staff->photo)
                                                <img src="{{ asset('uploads/office_chiefs/' . $staff->photo) }}" width="120" />
                                            @else

                                            @endif
                                        </td>
                                       <td>{{$staff->date_ad}}</td>
                                       <td>{{$staff->date_bs}}</td>
                                    </tr>
                                @endforeach
                            </table>
</div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
