@php
use App\Models\ExternalTool;

$tools = ExternalTool::where([
    'is_active' => '1',
    'is_deleted' => '0',
])->get();
@endphp

<div class="utility-group">
    <button
        type="button"
        class="utility-tools-toggle"
        aria-label="Show external tools"
        aria-expanded="false"
        aria-controls="external-tools-panel"
        title="External Tools – Show"
        onclick="const panel = document.getElementById('external-tools-panel'); const expanded = this.getAttribute('aria-expanded') === 'true'; panel.hidden = expanded; this.setAttribute('aria-expanded', expanded ? 'false' : 'true'); this.setAttribute('aria-label', expanded ? 'Show external tools' : 'Hide external tools'); this.title = expanded ? 'External Tools – Show' : 'External Tools – Hide'; this.querySelector('.utility-toggle-label').textContent = expanded ? 'Show' : 'Hide'; this.querySelector('.utility-toggle-state i').className = expanded ? 'fa fa-chevron-down' : 'fa fa-chevron-up';"
    >
        <i class="fa fa-th" aria-hidden="true"></i>
        <span class="utility-toggle-label">Show</span>
        <span class="utility-toggle-state" aria-hidden="true">
            <i class="fa fa-chevron-down"></i>
        </span>
    </button>

    <div class="utility-tools-panel" id="external-tools-panel" hidden>
        <ul class="utility-tools-grid">
            @foreach ($tools as $tool)
                <li>
                    <button
                        type="button"
                        class="utility-tool-card"
                        data-tool-url="{{ $tool->tool_url }}"
                        onclick="goTool(this.getAttribute('data-tool-url'))"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="{{ $tool->hover_text }}"
                        aria-label="{{ $tool->display_text }}"
                    >
                        <span class="utility-tool-inner">
                            <span class="utility-tool-icon">{!! $tool->tool_icon !!}</span>
                            <span class="utility-tool-label">{{ $tool->display_text }}</span>
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
