@extends('layout.client.container_2021')
@section('content')

    @php
    use App\Models\Office;
    $languageId = languageId();
    // dd($subdomain);
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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('guideline', $languageId) }}
                            </li>
                            <li class="breadcrumb-item"><a
                                href="{{ $abs_site_url . $subdomain . '/guidelines' }}">{{ \Helper::getNameOftext('guidelines', $languageId) }}</a>
                        </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('guideline', $languageId) }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if (count($guidelines) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed" id="normal_table">
                                        <thead>
                                            <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                            <th width="50%">{{ \Helper::getNameOftext('title', $languageId) }}</th>
                                            <th>{{ \Helper::getNameOftext('published-date', $languageId) }}</th>
                                            <th>{{ \Helper::getNameOftext('download', $languageId) }}</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count = 1;
                                            @endphp
                                            @foreach ($guidelines as $guideline)
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>
                                                        @if ($guideline->guideline_url)
                                                            <a
                                                                href="{{ route('guideline.detail.slug', ['subdomain' => $subdomain, 'url' => $guideline->guideline_url]) }}">
                                                                {{ $guideline->guideline_heading }}</a>
                                                        @else
                                                            <a href="#">
                                                                {{ $guideline->guideline_heading }}</a>
                                                        @endif
                                                    </td>
                                                    @if ($languageId == 1)
                                                        <td><i
                                                                class="far fa-calendar-alt"></i>{{ \Calendar::eng_to_nep_dor($guideline->published_date) }}
                                                        </td>
                                                    @else
                                                        <td><i class="far fa-calendar-alt"></i>
                                                            {{ $guideline->en_published_date }}</td>
                                                    @endif

                                                    <td>
                                                        @if (file_exists('uploads/guideline/' . $guideline->guideline_attachment) && $guideline->guideline_attachment)
                                                            <span><a href="{{ '/' . $subdomain . $guideline->office_subdomain . '/guideline/force/' . $guideline->guideline_url }}"
                                                                    target="_blank">
                                                                    <i class="fa fa-download"></i>
                                                                    {{ \Helper::getNameOftext('download', $languageId) }}
                                                                </a>
                                                            </span>
                                                        @else
                                                            {{ \Helper::getNameOftext('file-not-found', $languageId) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div>
                                    @else
                                        <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
                        </div>

                        {{ $guidelines->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
