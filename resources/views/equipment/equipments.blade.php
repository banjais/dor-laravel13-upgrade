@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'उपकरण' : 'Equipments';
    $notAvailableMessage = $languageId == 1 ? 'कुनैपनि रेकर्ड पाइएन ।' : 'Records are Not Available.';
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
                            <li class="breadcrumb-item">{{ $heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $heading }}</h2>
                                <div class="divider"></div>
                            </div>

                        </div>
                        @if (count($equipments) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed" id="project_table">
                                @if ($languageId == 1)
                                    <th>क्र.स</th>
                                    <th>उपकरणको नाम</th>
                                    <th>उपकरणको पहिचान</th>
                                    <th> श्रेणी / क्षमता</th>
                                    <th>डिओर कोड </th>
                                    <th>प्रती घण्टा रु.</th>
                                    <th>प्रती शिफ्ट रु.</th>
                                @else
                                    <th>S N</th>
                                    <th>Equipment Name</th>
                                    <th>Equipment Identification</th>
                                    <th>Category/Capacity </th>
                                    <th>Dor code </th>
                                    <th>Per Hour Rs.</th>
                                    <th>Per Shift Rs.</th>
                                @endif

                                @php
                                    $count = 1;
                                @endphp
                                @foreach ($equipments as $equipment)
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>{{ $equipment->equipment_name }}</td>
                                        <td>{{ $equipment->equipment_identification }}</td>
                                        <td>{{ $equipment->equipment_capacity_category }}</td>
                                        <td>{{ $equipment->equipment_dor_code }}</td>
                                        <td>{{ $equipment->equipment_per_hour }}</td>
                                        <td>{{ $equipment->equipment_per_shift }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            <div>
                            {{ $equipments->links() }}
                        @else
                            <div>{{ $notAvailableMessage }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
