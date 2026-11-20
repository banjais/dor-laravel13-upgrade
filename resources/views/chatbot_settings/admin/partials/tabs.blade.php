@php
    $chatbotTabs = [
        ['label' => 'General', 'route' => 'admin.chatbot-settings.index', 'active' => request()->routeIs('admin.chatbot-settings.*')],
        ['label' => 'Internal AI', 'route' => 'admin.chatbot-internal-providers.index', 'active' => request()->routeIs('admin.chatbot-internal-providers.*')],
        ['label' => 'External API', 'route' => 'admin.external-api-settings.index', 'active' => request()->routeIs('admin.external-api-settings.*')],
        ['label' => 'Knowledge', 'route' => 'admin.chatbot-knowledge.index', 'active' => request()->routeIs('admin.chatbot-knowledge.*')],
    ];
@endphp

<div class="chatbot-settings-tabs" style="display:flex;flex-wrap:wrap;gap:8px;margin:0 0 22px;padding:0 0 12px;border-bottom:1px solid #ddd;">
    @foreach($chatbotTabs as $tab)
        <a href="{{ route($tab['route']) }}"
           style="display:inline-block;padding:9px 16px;border-radius:5px;text-decoration:none;
           color:{{ $tab['active'] ? '#fff' : '#263746' }};
           background:{{ $tab['active'] ? '#1769aa' : '#edf1f5' }};
           font-weight:600;">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
