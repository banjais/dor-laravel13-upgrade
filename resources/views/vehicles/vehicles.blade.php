@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'सवारी साधन ' : 'Vehicles';
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
                        <div class="row">
                            <div>
                                <h3 class="hentry__title"><strong>{{ $heading }}</strong></h3>
                                <div class="col-lg-12">

                                    <?php if(count($vehicles) > 0){ ?>
                                        <div class="table-responsive">
                                    <table class="table table-striped table-condensed" id="vehicle_table">
                                        @if ($languageId == 1)
                                            <th>क्र.स</th>
                                            <th>विवरण</th>
                                            <th>Vehicle number</th>
                                            <th>अवस्था</th>
                                            <th>Status </th>

                                        @else
                                            <th>S N</th>
                                            <th>Type</th>
                                            <th>Vehicle number</th>
                                            <th>Condition</th>
                                            <th>Condition Type</th>

                                        @endif

                                        <?php
                                        $count = 1;
                                        foreach ($vehicles as $vehicle) {
                                            echo '<tr>';
                                            echo '<td>' . $count++ . '</td>';
                                            echo '<td>' . $vehicle->vehicle_type . '</td>';
                                            echo '<td>' . $vehicle->vehicle_number . '</td>';
                                            echo '<td>' . $vehicle->condition . '</td>';
                                            echo '<td>' . ($vehicle->condition_type == '1' ? 'Running' : 'Stopped') . '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </table>
                                    <div>
                                    <?php echo $vehicles->links(); ?>
                                    <?php } else { ?>
                                    <p>{{ $notAvailableMessage }}</p>
                                    <?php } ?>
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
