@php
use App\Models\Office;
use App\Models\Publication;
use App\Models\Category;
use App\Models\Download;
use App\Models\Notice;
use App\Models\Law;
use App\Models\News;
use App\Models\Guideline;

$categoryId = Category::getIdfromUrl($officeId, 'dor-news-letters');

$downloads = Download::getActiveDownloads($officeId, $languageId, 100);
$publications = Publication::getHomePagePublications($officeId, $languageId, 100);
$notices = Notice::getHomePageNotices($officeId, $languageId, 21);
$roadLaws = Law::getActiveLaws($languageId, 100);
$sidebarnews = News::getHomePageNews($officeId, $languageId, 100);
// dd($sidebarnews);
$guidelines = Guideline::getActiveGuidelines($languageId, 100);

$download_subdomain = $subdomain;
$publication_subdomain = $subdomain;
$notice_subdomain = $subdomain;
$guidelines_subdomain = $subdomain;
$sidebarnews_subdomain = $subdomain;

@endphp
<!-- Nav tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button"
            role="tab" aria-controls="tab1" aria-selected="true">{{ $languageId == 1 ? 'सुचना' : 'Notices' }}</button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab"
            aria-controls="tab3" aria-selected="false">{{ $languageId == 1 ? 'प्रकाशन' : 'Publications' }}</button>
    </li>
    @if ($officeId == 4)
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab"
            aria-controls="tab2" aria-selected="false">{{ $languageId == 1 ? 'म्यानुअल' : 'Manuals' }}</button>
    </li>
    @endif
    @if ($officeId == 4)
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab10-tab" data-bs-toggle="tab" data-bs-target="#tab10" type="button"
            role="tab" aria-controls="tab10"
            aria-selected="false">{{ $languageId == 1 ? 'सडक सम्बन्धी कानुन' : 'Road Laws' }}</button>
    </li>
    @endif

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab11-tab" data-bs-toggle="tab" data-bs-target="#tab11" type="button"
            role="tab" aria-controls="tab11" aria-selected="false">{{ $languageId == 1 ? 'समाचार' : 'News' }}</button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab12-tab" data-bs-toggle="tab" data-bs-target="#tab12" type="button"
            role="tab" aria-controls="tab12" aria-selected="false">{{ $languageId == 1 ? 'डाउनलोड' : 'Downloads' }}</button>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content" data-office-id="{{ $officeId }}">
    {{-- Notices --}}
    <div class="tab-pane active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
        <div class="bulleted-list">
            @if ($officeId == 4 || count($notices) > 0)
<ul>
                @foreach ($notices as $notice)
                    @php
                        $officeData_x = Office::getOfficeDetailsWithLanguages($notice->office_id, $languageId);
                        // dd($notice);
                        // dd(\Calendar::eng_to_nep_dor($notice->published_date));
                    @endphp
                    <li>
                        @if ($notice->notice_url)
                            <h3 class="media-heading">
                                <a
                                    href="{{ route('notice.detail.slug', ['subdomain' => $officeData_x[0]->office_subdomain,'url' => $notice->notice_url]) }}">{{ $notice->notice_heading }}</a>
                            </h3>
                        @else
                            <h3 class="media-heading">
                                <a href="#">{{ $notice->notice_heading }}</a>
                            </h3>
                        @endif
                        <p>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                            {{ $languageId == 1 ? \Calendar::eng_to_nep_dor($notice->published_date) : $notice->en_published_date }}
                        <p><small>Office :</small>
                            @if ($languageId == 1)
                                <span><a
                                        href="{{ '/' . $officeData_x[0]->office_subdomain }}">{{ $officeData_x[0]->office_name }}</a></span>
                            @else
                                <span><a
                                        href="{{ '/' . $officeData_x[0]->office_subdomain }}">{{ $officeData_x[0]->office_name }}</a></span>
                            @endif
                        </p>
                        </p>

                    </li>
                @endforeach
            </ul>
@else
<p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
@endif
        </div>
        @if ($officeId == 4 || count($notices) > 20)
<div class="mt-3">
            <a href="{{ '/'.$subdomain }}/notices"
                class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
@endif
    </div>

    {{-- Publications --}}
    <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
        <div class="bulleted-list">
            @if (count($publications) > 0)
                <ul>
                    @foreach ($publications as $publicationCategory)
                        @if (isset($publicationCategory->details))
                            @foreach ($publicationCategory->details as $detail)
                                <li>
                                    <h3>
                                        <a
                                            href="{{ '/' . $subdomain }}/publication/{{ $publicationCategory->category_url }}">{{ $detail->category_heading }}</a>
                                    </h3>
                                </li>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @else
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            @endif
        </div>
        @if ($officeId == 4 || count($publications) > 20)
<div class="mt-3">
            <a href="{{ '/' . $subdomain }}/publications"
                class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
@endif
    </div>

    @if ($officeId == 4)
    {{-- Guidelines --}}
    <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
        <div class="bulleted-list">
            @if (count($guidelines) > 0)
                <ul>
                    @foreach ($guidelines as $guideline)
                        <li>
                            <h3><a
                                    href="{{ '/' . $guidelines_subdomain }}/guideline/{{ $guideline->guideline_url }}">{{ $guideline->guideline_heading }}</a>
                            </h3>
                            <p>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                {{ $languageId == 1 ? \Calendar::eng_to_nep_dor($guideline->published_date) : $guideline->en_published_date }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            @endif
        </div>
        <div class="mt-3">
            <a href="{{ '/' . $subdomain }}/guidelines" class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
    </div>

    @endif

    @if ($officeId == 4)
    {{-- Road Laws --}}
    <div class="tab-pane" id="tab10" role="tabpanel" aria-labelledby="tab10-tab">
        <div class="bulleted-list">
            @if (count($roadLaws) > 0)
                <ul>
                    @foreach ($roadLaws as $item)
                        <li>
                            <h3><a
                                    href="{{ '/' . $subdomain }}/law/{{ $item->law_url }}">{{ $item->law_heading }}</a>
                            </h3>
                            <p>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                {{ $languageId == 1 ? \Calendar::eng_to_nep_dor($item->published_date) : $item->en_published_date }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            @endif
        </div>
        <div class="mt-3">
            <a href="{{ '/' . $subdomain }}/laws" class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
    </div>

    @endif

    {{-- DOR News --}}
    <div class="tab-pane" id="tab11" role="tabpanel" aria-labelledby="tab11-tab">
        <div class="bulleted-list">
            @if (count($sidebarnews) > 0)
                <ul>
                    @foreach ($sidebarnews as $item)
                        @php
                            // dd($item);
                        @endphp
                        <li>
                            <h3><a
                                    href="{{ '/' . $sidebarnews_subdomain }}/news/{{ $item->news_url }}">{{ $item->news_heading }}</a>
                            </h3>
                            <p>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                {{ $languageId == 1 ? \Calendar::eng_to_nep_dor($item->published_date) : $item->en_published_date }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            @endif
        </div>
        @if ($officeId == 4 || count($sidebarnews) > 20)
<div class="mt-3">
            <a href="{{ '/' . $subdomain }}/news" class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
@endif
    </div>

    {{-- Downloads --}}
    <div class="tab-pane" id="tab12" role="tabpanel" aria-labelledby="tab12-tab">
        <div class="bulleted-list">
            @if (count($downloads) > 0)
                <ul>
                    @foreach ($downloads as $item)
                        @php
                            // dd($item);
                        @endphp
                        <li>
                            <h3><a
                                    href="{{ '/' . $download_subdomain }}/download/{{ $item->download_url }}">{{ $item->download_heading }}</a>
                            </h3>
                            <p>{{ \Helper::getNameOftext('published-date', $languageId) . ' : ' }}
                                {{ $languageId == 1 ? \Calendar::eng_to_nep_dor($item->published_date) : $item->en_published_date }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            @endif
        </div>
        @if ($officeId == 4 || count($downloads) > 20)
<div class="mt-3">
            <a href="{{ '/' . $subdomain }}/downloads" class="more-btn">{{ \Helper::getNameOftext('read-more', $languageId) }}</a>
        </div>
@endif
    </div>
</div>

<script>
(function () {
    function syncDoRHomepageTabHeights() {
        var board = document.getElementById('download-tabs-section');
        if (!board) return;

        var panes = Array.prototype.slice.call(
            board.querySelectorAll('.tab-content > .tab-pane')
        );
        var notice = board.querySelector('#tab1');
        var content = board.querySelector('.tab-content');
        var documents = board.querySelector('#tab3');
        var isOtherOffice = content && content.getAttribute('data-office-id') !== '4';

        if (!notice || !content || !panes.length) return;

        var list = documents && documents.querySelector('.bulleted-list');

        // Other Offices: show the complete lists without any scrollbar.
        // Read More handles navigation to the remaining records.
        if (isOtherOffice) {
            panes.forEach(function (pane) {
                ['height', 'max-height', 'overflow-y', 'overflow-x', 'box-sizing']
                    .forEach(function (property) {
                        pane.style.removeProperty(property);
                    });

                var tabList = pane.querySelector('.bulleted-list');
                if (tabList) {
                    ['height', 'max-height', 'overflow-y', 'overflow-x']
                        .forEach(function (property) {
                            tabList.style.removeProperty(property);
                        });
                }
            });
            return;
        }

        if (window.innerWidth < 992) {
            panes.forEach(function (pane) {
                ['height', 'max-height', 'overflow-y', 'overflow-x', 'box-sizing']
                    .forEach(function (property) {
                        pane.style.removeProperty(property);
                    });

                var mobileList = pane.querySelector('.bulleted-list');
                if (mobileList) {
                    ['max-height', 'overflow-y', 'overflow-x']
                        .forEach(function (property) {
                            mobileList.style.removeProperty(property);
                        });

                    if (isOtherOffice) {
                        var mobileItems = mobileList.querySelectorAll('ul li').length;
                        if (mobileItems > 20) {
                            mobileList.style.maxHeight = '300px';
                            mobileList.style.overflowY = 'auto';
                            mobileList.style.overflowX = 'hidden';
                        }
                    }
                }
            });
            return;
        }

        // Measure a hidden copy of Notice; do not change the visible Notice panel.
        var copy = notice.cloneNode(true);
        copy.removeAttribute('id');
        copy.style.setProperty('display', 'block', 'important');
        copy.style.setProperty('position', 'absolute', 'important');
        copy.style.setProperty('left', '-10000px', 'important');
        copy.style.setProperty('top', '0', 'important');
        copy.style.setProperty('visibility', 'hidden', 'important');
        copy.style.setProperty('width', content.clientWidth + 'px', 'important');
        copy.style.setProperty('height', 'auto', 'important');
        copy.style.setProperty('max-height', 'none', 'important');
        copy.style.setProperty('overflow', 'visible', 'important');

        board.appendChild(copy);
        var targetHeight = Math.ceil(copy.getBoundingClientRect().height);
        copy.remove();

        if (!targetHeight) return;

        panes.forEach(function (pane) {
            pane.style.height = 'auto';
            pane.style.maxHeight = isOtherOffice
                ? targetHeight + 'px'
                : (pane === notice ? 'none' : targetHeight + 'px');
            pane.style.boxSizing = 'border-box';
            pane.style.overflowX = 'hidden';
            pane.style.overflowY = isOtherOffice
                ? 'visible'
                : (pane === notice ? 'visible' : 'auto');
        });

        if (isOtherOffice) {
            panes.forEach(function (pane) {
                var tabList = pane.querySelector('.bulleted-list');
                if (!tabList) return;

                ['max-height', 'overflow-y', 'overflow-x']
                    .forEach(function (property) {
                        tabList.style.removeProperty(property);
                    });

                var itemCount = tabList.querySelectorAll('ul li').length;
                if (itemCount <= 20) return;

                var paneStyle = window.getComputedStyle(pane);
                var readMore = pane.querySelector('.mt-3');
                var readMoreHeight = 0;

                if (readMore) {
                    var readMoreStyle = window.getComputedStyle(readMore);
                    readMoreHeight = readMore.getBoundingClientRect().height
                        + parseFloat(readMoreStyle.marginTop || 0)
                        + parseFloat(readMoreStyle.marginBottom || 0);
                }

                var listStyle = window.getComputedStyle(tabList);
                var available = targetHeight
                    - parseFloat(paneStyle.paddingTop || 0)
                    - parseFloat(paneStyle.paddingBottom || 0)
                    - readMoreHeight
                    - parseFloat(listStyle.marginTop || 0)
                    - parseFloat(listStyle.marginBottom || 0);

                tabList.style.maxHeight = Math.max(120, Math.floor(available)) + 'px';
                tabList.style.overflowY = 'auto';
                tabList.style.overflowX = 'hidden';
            });
        } else if (documents && list) {
            var readMore = documents.querySelector('.mt-3');
            var search = documents.querySelector('.document-search-wrap');
            var paneStyle = window.getComputedStyle(documents);

            function outerHeight(element) {
                if (!element) return 0;
                var style = window.getComputedStyle(element);
                return element.getBoundingClientRect().height
                    + parseFloat(style.marginTop || 0)
                    + parseFloat(style.marginBottom || 0);
            }

            var available = targetHeight
                - parseFloat(paneStyle.paddingTop || 0)
                - parseFloat(paneStyle.paddingBottom || 0)
                - outerHeight(readMore)
                - outerHeight(search);

            var listStyle = window.getComputedStyle(list);
            available -= parseFloat(listStyle.marginTop || 0);
            available -= parseFloat(listStyle.marginBottom || 0);

            list.style.maxHeight = Math.max(80, Math.floor(available)) + 'px';
            list.style.overflowY = 'auto';
            list.style.overflowX = 'hidden';
            documents.style.overflowY = 'visible';
        }
    }

    function scheduleHeightSync() {
        window.requestAnimationFrame(syncDoRHomepageTabHeights);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleHeightSync);
    } else {
        scheduleHeightSync();
    }

    window.addEventListener('load', scheduleHeightSync);
    window.addEventListener('resize', scheduleHeightSync);

    document.addEventListener('shown.bs.tab', function (event) {
        var board = document.getElementById('download-tabs-section');
        if (board && board.contains(event.target)) scheduleHeightSync();
    });
})();
</script>


<script>
(function () {
    function addOtherOfficeTabSearch() {
        var content = document.querySelector('.tab-content[data-office-id]');
        if (!content || content.getAttribute('data-office-id') === '4') return;

        ['tab1', 'tab3', 'tab11', 'tab12'].forEach(function (tabId) {
            var pane = content.querySelector('#' + tabId);
            if (!pane) return;

            var list = pane.querySelector('.bulleted-list ul');
            if (!list) return;

            var items = Array.prototype.slice.call(list.querySelectorAll(':scope > li'));
            if (items.length <= 20) return;

            var search = document.createElement('input');
            search.type = 'search';
            search.className = 'form-control form-control-sm other-office-tab-search';
            search.placeholder = 'Search this office...';
            search.setAttribute('aria-label', 'Search records in this office');
            search.style.marginBottom = '12px';

            var noMatch = document.createElement('p');
            noMatch.textContent = 'No matching records found.';
            noMatch.style.display = 'none';

            list.parentNode.insertBefore(search, list);
            list.parentNode.insertBefore(noMatch, list.nextSibling);

            search.addEventListener('input', function () {
                var term = search.value.trim().toLocaleLowerCase();
                var visible = 0;

                items.forEach(function (item) {
                    var match = item.textContent.toLocaleLowerCase().indexOf(term) !== -1;
                    item.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                noMatch.style.display = visible === 0 ? '' : 'none';
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addOtherOfficeTabSearch);
    } else {
        addOtherOfficeTabSearch();
    }
})();
</script>
