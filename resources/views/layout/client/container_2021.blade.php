<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.client.head_2021')
    @yield('more_css')
    @csrf
</head>

@php
$font_size_code = Session::get('font_size_code');
$font_size_classes = [
    'a-minus-2' => 'body_92',
    'a-minus' => 'body_96',
    'a' => 'body_100',
    'a-plus' => 'body_104',
    'a-plus-2' => 'body_108',
];
$body_class = $font_size_classes[$font_size_code] ?? 'body_100';

// dd();

@endphp

<body class="{{ $body_class }}">
    @include('layout.client.header_2021')
    @yield('content')
    @php
        $chatbotWidgetSettings = \App\Models\ChatbotSetting::current();
        $chatbotWidgetSubdomain = request()->route('subdomain') ?? 'home';
    @endphp
    @if($chatbotWidgetSettings->enabled)
        @include('chatbot.widget', [
            'settings' => $chatbotWidgetSettings,
            'subdomain' => $chatbotWidgetSubdomain
        ])
    @endif
    @include('layout.client.footer_2021')
    @include('layout.client.foot_2021')
    @yield('scripts')
    @yield('more_scripts')
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0&appId=1414260672139056&autoLogAppEvents=1"
        nonce="TInkG7lz"></script>

    <script>
        if ('serviceWorker' in navigator && window.isSecureContext) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js', {
                    scope: '/',
                    updateViaCache: 'none'
                }).catch(function (error) {
                    console.warn('DoR PWA registration failed:', error);
                });
            });
        }
    </script>
</body>

</html>
