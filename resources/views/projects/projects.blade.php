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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('projects', $languageId) }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('projects', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if (count($projects) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-condensed" id="project_table">
                                    <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('project_name', $languageId) }}</th>
                                    {{-- <th>{{ \Helper::getNameOftext('reference_number', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('link_code', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('bt', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('gr', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('er', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('total', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('uc', $languageId) }}</th>
                                    <th>{{ \Helper::getNameOftext('pl', $languageId) }}</th>
                                    <th></th> --}}

                                    <?php
                                    $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                                    $paginate = $projects->perPage();
                                    $count = ($page - 1) * $paginate + 1;
                                    foreach ($projects as $project) {
                                        echo '<tr>';
                                        echo '<td>' . $count++ . '</td>';
                                        echo '<td><a href="project/' . $project->project_url . '">' . $project->project_heading . '</a></td>';
                                        // echo '<td>' . $project->project_class . '</td>';
                                        // echo '<td>' . $project->project_ref_no . '</td>';
                                        // echo '<td>' . $project->project_link_code . '</td>';
                                        // echo '<td class="project_bt">' . $project->project_black_tapped . '</td>';
                                        // echo '<td>' . $project->project_gravel . '</td>';
                                        // echo '<td>' . $project->project_earthen . '</td>';
                                        // echo '<td>' . round($project->project_total, 2) . '</td>';
                                        // echo '<td>' . $project->project_under_construction . '</td>';
                                        // echo '<td>' . $project->project_plan . '</td>';
                                        // echo '<td><a href="project/' . $project->project_url . '">' . \Helper::getNameOftext('read-more', $languageId) . '</a></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </table>
                                </div>
                                {{ $projects->links() }}
                            @else
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
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
