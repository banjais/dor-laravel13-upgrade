@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = languageId();
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'प्रकाशन' : 'Publications';
    $sn = $languageId == 1 ? 'क्र.स' : 'S N';
    $title = $languageId == 1 ? 'प्रकाशन वर्ग' : 'Publication Category';
    $readmore = $languageId == 1 ? 'पुरा हेर्नुहोस्  ' : 'View all';
    $notAvailableMessage = $languageId == 1 ? 'प्रकाशन उपलब्ध छैन ।' : 'Publications are not available.';
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

                            @if ($subdomain === 'home' || ($subdomain !== 'home' && ($publicationCount ?? 0) > 20))
                            <div class="document-search-wrap publication-category-search">
                                <label for="document-publication-search">
                                    Search
                                </label>
                                <div class="document-search-control">
                                    <input type="search" id="document-publication-search"
                                        class="document-search-input" autocomplete="off"
                                        aria-controls="document-publication-suggestions"
                                        placeholder="Search">
                                    <div id="document-publication-suggestions"
                                        class="document-search-suggestions" role="listbox" hidden></div>
                                </div>
                            </div>
                            @endif
                            @if (count($categories) > 0)
                                @php
                                    $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                                    $paginate = $categories->perPage();
                                    $count = ($page - 1) * $paginate + 1;
                                @endphp
                                <div class="table-responsive">
                                <table class="table table-striped table-condensed" id="normal_table">
                                    <th>{{ $sn }}</th>
                                    <th>{{ $title }}</th>
                                    <th></th>
                                    <?php
                                    foreach ($categories as $category) {
                                        echo '<tr>';
                                        echo '<td>' . $count++ . '</td>';
                                        echo '<td><a href="publication/' . $category->category_url . '">' . $category->category_heading . '</a></td>';
                                        echo '<td><a href="publication/' . $category->category_url . '">' . $readmore . '</a></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </table>
                                </div>
                            @else
                                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
                            @endif
                        </div>

                        {{ $categories->links() }}
                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>


@if ($subdomain === 'home' || ($subdomain !== 'home' && ($publicationCount ?? 0) > 20))
<script>
(function () {
    var input = document.getElementById('document-publication-search');
    var box = document.getElementById('document-publication-suggestions');
    var endpoint = @json('/' . trim($subdomain, '/') . '/searchpublication');
    var timer = null;
    var requestId = 0;
    var results = [];
    var activeIndex = -1;

    function closeResults() {
        if (!box) return;
        box.hidden = true;
        box.replaceChildren();
        results = [];
        activeIndex = -1;
    }

    function chooseResult(item) {
        if (!item) return;

        if (item.type === 'suggestion') {
            input.value = item.value || '';
            closeResults();
            input.focus();
            return;
        }

        if (item.url) window.location.href = item.url;
    }

    function renderResults(items) {
        box.replaceChildren();
        results = Array.isArray(items) ? items : [];
        activeIndex = -1;

        if (!results.length) {
            var empty = document.createElement('div');
            empty.className = 'document-search-empty';
            empty.textContent = @json($languageId == 1 ? 'कुनै नतिजा भेटिएन' : 'No results found');
            box.appendChild(empty);
            box.hidden = false;
            return;
        }

        results.forEach(function (item, index) {
            var option = document.createElement('button');
            option.type = 'button';
            option.className = 'document-search-option';
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');
            var kind = item.type === 'suggestion'
                ? ''
                : (item.type === 'category'
                    ? @json($languageId == 1 ? 'फोल्डर: ' : 'Folder: ')
                    : @json($languageId == 1 ? 'फाइल: ' : 'File: '));
            option.textContent = kind + (item.label || item.value || '');
            if (item.type === 'suggestion') {
                option.classList.add('document-search-suggestion');
            }
            option.addEventListener('click', function () {
                chooseResult(results[index]);
            });
            box.appendChild(option);
        });
        box.hidden = false;
    }

    function updateActiveOption() {
        var options = box.querySelectorAll('.document-search-option');
        options.forEach(function (option, index) {
            var selected = index === activeIndex;
            option.classList.toggle('is-active', selected);
            option.setAttribute('aria-selected', selected ? 'true' : 'false');
            if (selected) option.scrollIntoView({ block: 'nearest' });
        });
    }

    if (input && box) {
        input.addEventListener('input', function () {
            var term = input.value.trim();
            var currentRequest = ++requestId;
            window.clearTimeout(timer);

            if (!term) {
                closeResults();
                return;
            }

            timer = window.setTimeout(function () {
                fetch(endpoint + '?term=' + encodeURIComponent(term), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Search failed');
                    return response.json();
                })
                .then(function (items) {
                    if (currentRequest !== requestId) return;
                    renderResults(items);
                })
                .catch(function () {
                    if (currentRequest === requestId) closeResults();
                });
            }, 120);
        });

        input.addEventListener('keydown', function (event) {
            var options = box.querySelectorAll('.document-search-option');
            if (event.key === 'ArrowDown' && options.length) {
                event.preventDefault();
                activeIndex = Math.min(activeIndex + 1, options.length - 1);
                updateActiveOption();
            } else if (event.key === 'ArrowUp' && options.length) {
                event.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveOption();
            } else if (event.key === 'Enter' && activeIndex >= 0) {
                event.preventDefault();
                chooseResult(results[activeIndex]);
            } else if (event.key === 'Escape') {
                closeResults();
            }
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.document-search-control')) closeResults();
        });
    }
})();
</script>
@endif

@stop
