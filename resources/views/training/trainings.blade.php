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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('trainings', $languageId) }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('trainings', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <div class="col-lg-12">

                                    <?php if(count($trainings) > 0){ ?>
                                    {{-- <div class="mb-2">
                                        <form href="" name="searchForm" id="searchForm" method="post">
                                            {{ $from }} <input name="expiry_from" class="date-picker" />
                                            {{ $to }} <input name="expiry_to" class="date-picker" />
                                            <input type="submit" value="submit" name="submit">
                                        </form>
                                    </div> --}}
                                    <div class="table-responsive">
                                    <table class="table table-striped table-condensed" id="training_table">

                                        <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                        <th>{{ \Helper::getNameOftext('title', $languageId) }} </th>
                                        <th>{{ \Helper::getNameOftext('start_date', $languageId) }}</th>
                                        <th>{{ \Helper::getNameOftext('end_date', $languageId) }}</th>
                                        <th>{{ \Helper::getNameOftext('contact_person', $languageId) }}</th>
                                        <th>{{ \Helper::getNameOftext('contact_number', $languageId) }}</th>
                                        <th>{{ \Helper::getNameOftext('number_of_trainees', $languageId) }}</th>
                                        <th></th>

                                        <?php
                                        $count = 1;
                                        foreach ($trainings as $training) {
                                            echo '<tr>';
                                            echo '<td>' . $count++ . '</td>';
                                            echo '<td><a href="training/' . $training->training_url . '">' . $training->training_heading . '</a></td>';

                                            if ($languageId == 1) {
                                                if ($training->training_start_date) {
                                                    echo '<td><i class="fa fa-calendar me-1"></i>' . \Calendar::eng_to_nep_dor($training->training_start_date) . '</td>';
                                                }
                                                if ($training->training_end_date) {
                                                    echo '<td><i class="fa fa-calendar me-1"></i>' . \Calendar::eng_to_nep_dor($training->training_end_date) . '</td>';
                                                }
                                            } else {
                                                echo '<td><i class="class="fa fa-calendar me-1"></i>' . $training->en_training_start_date . '</td>';
                                                echo '<td><i class="class="fa fa-calendar me-1"></i>' . $training->en_training_end_date . '</td>';
                                            }

                                            echo '<td>' . $training->training_contact_person . '</td>';
                                            echo '<td>' . $training->training_contact_number . '</td>';
                                            echo '<td class="number_of_traniees">' . $training->number_of_traniees . '</td>';
                                            echo '<td><a href="training/' . $training->training_url . '">' . \Helper::getNameOftext('read-more', $languageId) . '</a></td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </table>
                                    </div>
                                    <?php echo $trainings->links(); ?>
                                    <?php } else { ?>
                                    <p>{{ $notAvailableMessage }}</p>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
        </section>
    </main>
@stop
