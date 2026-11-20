@extends('layout.client.container_2021')
@section('content')

    @php
    use App\Models\Office;
    $languageId = languageId();
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
                            <li class="breadcrumb-item">{{ \Helper::getNameOftext('news', $languageId) }}
                            </li>
                            @if (isset($office_name))
                                <li class="breadcrumb-item"><a
                                        href="{{ $abs_site_url . $subdomain . '/notices' }}">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item">{{ \Helper::getNameOftext('notices', $languageId) }}</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ \Helper::getNameOftext('notices', $languageId) }}
                                    {{ isset($office_name) ? ' :- ' . $office_name : '' }}</h2>
                                <div class="divider"></div>
                            </div>

                            <div class="form-wrap">
                                <form action="" method="GET" id="livesearch">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-6">
                                            <div class="form-group">
                                                @if ($subdomain === 'home')
                                                    <label for="notice-search-term">Search by Name / Content</label>
                                                    <input type="text" id="notice-search-term" name="term"
                                                        class="input-text form-control"
                                                        placeholder="Enter office name or notice title..."
                                                        value="{{ Request::get('term', Request::get('office_name')) }}"
                                                        autocomplete="off" />
                                                @else
                                                    <label for="notice-search-term">Search Notice Title / Content</label>
                                                    <input type="text" id="notice-search-term" name="term"
                                                        class="input-text form-control"
                                                        placeholder="Search this office's notices..."
                                                        value="{{ Request::get('term') }}"
                                                        autocomplete="off" />
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-lg-2">
                                            <div class="form-group">
                                                <label
                                                    for="">{{ \Helper::getNameOftext('date-from', $languageId) }}</label>
                                                <input name="from" class="date-picker form-control"
                                                    value="{{ Request::get('from') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-lg-2">
                                            <div class="form-group">
                                                <label
                                                    for="">{{ \Helper::getNameOftext('date-to', $languageId) }}</label>
                                                <input name="to" class="date-picker form-control"
                                                    value="{{ Request::get('to') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mt-4">
                                                <input type="submit" value="Search" class="submit-btn w-100" />
                                            </div>
                                        </div>

                                    </div>
                                </form>
                                @if (!empty($didYouMean) && $subdomain === 'home')
                                    <p class="mt-2">
                                        Did you mean:
                                        <a href="{{ $abs_site_url }}home/notices?term={{ urlencode($didYouMean) }}">
                                            {{ $didYouMean }}?
                                        </a>
                                    </p>
                                @endif
                            </div>
                            @if (count($notices) > 0)
                                @php
                                    $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                                    $paginate = $notices->perPage();
                                    $count = ($page - 1) * $paginate + 1;
                                @endphp
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-condensed" id="normal_table">
                                        <thead>
                                            <th>{{ \Helper::getNameOftext('serial', $languageId) }}</th>
                                            <th width="50%">{{ \Helper::getNameOftext('title', $languageId) }}</th>
                                            <th>{{ \Helper::getNameOftext('office', $languageId) }}</th>
                                            <th>{{ \Helper::getNameOftext('published-date', $languageId) }}</th>
                                            <th>{{ \Helper::getNameOftext('download', $languageId) }}</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count = 1;
                                            @endphp
                                            @foreach ($notices as $notice)
                                                @php
                                                    // dd($notice);
                                                    $officeData = Office::getOfficeDetailsWithLanguages($notice->office_id, $languageId);
                                                @endphp

                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>
                                                        @if ($notice->notice_url)
                                                            <a
                                                                href="{{ route('notice.detail.slug', ['subdomain' => $officeData[0]->office_subdomain, 'url' => $notice->notice_url]) }}">
                                                                {{ $notice->notice_heading }}</a>
                                                        @else
                                                            <a href="#">
                                                                {{ $notice->notice_heading }}</a>
                                                        @endif
                                                    </td>
                                                    @if ($languageId == 1)
                                                        <td><a
                                                                href="{{ $abs_site_url . $officeData[0]->office_subdomain }}">
                                                                {{ $officeData[0]->office_name }}</a></td>
                                                    @else
                                                        <td><a
                                                                href="{{ $abs_site_url . $officeData[0]->office_subdomain }}">
                                                                {{ $officeData[0]->office_name }}</a></td>
                                                    @endif

                                                    @if ($languageId == 1)
                                                        <td><i
                                                                class="far fa-calendar-alt"></i>{{ \Calendar::eng_to_nep_dor($notice->published_date) }}
                                                        </td>
                                                    @else
                                                        <td><i class="far fa-calendar-alt"></i>
                                                            {{ $notice->en_published_date }}</td>
                                                    @endif

                                                    <td>
                                                        @if (file_exists('uploads/notice/' . $notice->notice_attachment) && $notice->notice_attachment)
                                                            <span><a href="{{ '/' . $notice->office_subdomain . '/notice/force/' . $notice->notice_url }}"
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

                        {{ $notices->appends($_GET)->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
    <link rel="stylesheet" href="{{ asset('assets/css_2021/ui-lightness/jquery-ui-1.10.4.custom.min.css') }}"
        type="text/css" />
<style>
    .notice-search-autocomplete-menu .ui-menu-item a {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 9px 11px;
        white-space: normal;
        line-height: 1.5;
    }

    .notice-suggestion-badge {
        flex: 0 0 auto;
        min-width: 57px;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        letter-spacing: .3px;
    }

    .notice-suggestion-badge.office {
        color: #1769aa;
        background: #eaf3fb;
    }

    .notice-suggestion-badge.notice {
        color: #455468;
        background: #edf0f3;
    }

    .notice-suggestion-text {
        flex: 1;
        white-space: normal;
        overflow-wrap: anywhere;
    }
</style>
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js_2021/jquery-ui-1.10.4.custom.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
// Search offices and notice titles/content on the main Notice page.
            @if ($subdomain === 'home')
                $("#notice-search-term").autocomplete({
                    source: function(request, response) {
                        $.getJSON("{{ $abs_site_url }}home/searchnotice", {
                            term: request.term
                        }, response);
                    },
                    minLength: 2,
                    create: function() {
                        var autocomplete = $(this).data("ui-autocomplete");
                        autocomplete.widget().addClass("notice-search-autocomplete-menu");

                        autocomplete._renderItem = function(ul, item) {
                            var isOffice = item.type === "office";
                            var isSuggestion = item.type === "suggestion";

                            var badge = $("<span>")
                                .addClass("notice-suggestion-badge")
                                .addClass(isSuggestion ? "suggestion" : (isOffice ? "office" : "notice"))
                                .text(isSuggestion ? "DID YOU MEAN" : (isOffice ? "OFFICE" : "NOTICE"));

                            var description = $("<span>")
                                .addClass("notice-suggestion-text")
                                .text(isOffice ? item.value : item.label);

                            var link = $("<a>")
                                .attr("href", "#")
                                .append(badge)
                                .append(description);

                            return $("<li>")
                                .append(link)
                                .appendTo(ul);
                        };
                    },
                    select: function(event, ui) {
                        if (ui.item.type === "suggestion") {
                            $("#notice-search-term").val(ui.item.value);
                            $(this).autocomplete("close");
                            return false;
                        }

                        if (ui.item.url) {
                            window.location.href = ui.item.url;
                            return false;
                        }
                    }
                });
            @endif
            // date picker
            $(".date-picker").datepicker({
                dateFormat: 'yy-mm-dd'
            });

        });
    </script>
@endsection
@stop
