@extends('layout.client.container_2021')
@section('content')
    <?php
    use App\Models\Home;
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $selectDor = $languageId == 1 ? 'कार्यालय छान्नुहोस्' : 'Select DOR Office';
    // dd($url);
    ?>

    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $contentData->content_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="sidebar-wrap">
                            <div class="sidebar-list">
                                @if (count($childContents) > 0)
                                    <ul>
                                        @foreach ($childContents as $content)
                                            <li class="{{ $contentData->id == $content->id ? 'active' : '' }}">
                                                <a
                                                    href="{{ $content->content_url }}">{{ $content->content_heading }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <ul>
                                        @foreach ($parentContents as $content)
                                            <li class="{{ $contentData->id == $content->id ? 'active' : '' }}">
                                                <a
                                                    href="{{ $content->content_url }}">{{ $content->content_heading }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($subdomain == 'home' && $url == 'office-head-upto-date')
                        <div class="col-md-9">
                            @include('office_chief.staffs_partial')
                        </div>
                    @else
                        <div class="col-md-9">
                            <div class="block-title mt-3 mt-md-0">
                                <h2>{{ $contentData->content_heading }}</h2>
                                <div class="divider">
                                </div>
                            </div>
                            <div class="block-list mt-4">
                                {!! preg_replace("~https?://dor\\.gov\\.np(?=/)~i", "", $contentData->content_description) !!}
                                @php
                                   $exclude_download_urls = ['organisation-structure'];
                                @endphp
                                @if(!in_array($url, $exclude_download_urls))
                                    @if ($contentData->content_attachment)
                                        @if (file_exists('uploads/pages/' . $contentData->content_attachment))
                                            <p><a href="{{ $abs_site_url . $subdomain }}/page/mainforce/{{ $contentData->content_url }}/{{$contentData->office_id}}"
                                                    target="_blank"><i class="fa fa-download"></i>
                                                    {{ \Helper::getNameOftext('download', $languageId) }}</a></p>
                                        @endif
                                    @endif
                                @endif

                                @php
                                    $listLev2Header = Home::headerContentAllList($contentData->id, $contentData->parent_id);
                                    // print_r(count($listLev2Header));exit;
                                    echo Helper::GetSubContentDoclist($contentData->content_url, $contentData->id, '1');
                                @endphp
                            </div>
                        </div>

                    @endif
                </div>
        </section>
    </main>

@stop
