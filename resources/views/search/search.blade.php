@extends('layout.client.container_2021')
@section('content')
    <?php
    $languageId = Session::get('language_id');
    $home = 'Home';
    $contentHeader = 'Related Pages';
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
                            <li class="breadcrumb-item">Search
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <script>
                        (function() {
                            var cx = '004184895998473855276:x5lwkhntaay';
                            var gcse = document.createElement('script');
                            gcse.type = 'text/javascript';
                            gcse.async = true;
                            gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
                                '//cse.google.com/cse.js?cx=' + cx;
                            var s = document.getElementsByTagName('script')[0];
                            s.parentNode.insertBefore(gcse, s);
                        })();
                    </script>
                    <gcse:searchresults-only></gcse:searchresults-only>
                </div>
            </div>
        </section>
    </main>


@stop
