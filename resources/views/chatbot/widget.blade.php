<div id="dor-chatbot"
     data-subdomain="{{ $subdomain }}"
     data-token="{{ csrf_token() }}"
     data-position="{{ $settings->position }}"
     data-bot-name="{{ $settings->bot_name }}"
     data-welcome="{{ $settings->welcome_message ?: 'नमस्कार! म DoR Assistant हुँ। तपाईंलाई के जानकारी चाहिन्छ?' }}"
     data-panel="{{ $settings->panel_color }}"
     data-header="{{ $settings->header_color }}"
     data-user="{{ $settings->user_message_color }}"
     data-bot="{{ $settings->bot_message_color }}"
     data-text="{{ $settings->text_color }}"
     data-voice="{{ $settings->voice_enabled ? '1' : '0' }}"
     data-speech-input="{{ $settings->speech_input_enabled ? '1' : '0' }}"
     data-speech-output="{{ $settings->voice_output_enabled ? '1' : '0' }}">
    <button type="button" id="dor-chatbot-toggle" aria-label="Open DoR Assistant">
        <span aria-hidden="true">💬</span> सहायता
    </button>

    <section id="dor-chatbot-panel" hidden aria-label="DoR Assistant">
        <header>
            <strong id="dor-chatbot-title"></strong>
            <button type="button" id="dor-chatbot-close" aria-label="Close">×</button>
        </header>
        <div id="dor-chatbot-messages" aria-live="polite"></div>
        <div class="dor-chatbot-input-row">
            <textarea id="dor-chatbot-question" rows="2"
                maxlength="2000" placeholder="आफ्नो प्रश्न लेख्नुहोस्..."></textarea>
            <button type="button" id="dor-chatbot-send">पठाउनुहोस्</button>
        </div>
        <div id="dor-chatbot-status" role="status"></div>
    </section>
</div>

<style>
#dor-chatbot{position:fixed;z-index:99990;bottom:22px;font-family:inherit}
#dor-chatbot[data-position="bottom_left"]{left:20px}
#dor-chatbot:not([data-position="bottom_left"]){right:20px}
#dor-chatbot-toggle{border:0;border-radius:24px;padding:12px 19px;background:#142d4e;color:#fff;font-weight:700;box-shadow:0 3px 14px #0003;cursor:pointer}
#dor-chatbot-panel{position:absolute;bottom:60px;right:0;width:min(370px,calc(100vw - 28px));height:min(540px,72vh);background:var(--dor-panel);color:var(--dor-text);border:1px solid #ccd3dc;border-radius:12px;box-shadow:0 8px 30px #0003;overflow:hidden;display:flex;flex-direction:column}
#dor-chatbot[data-position="bottom_left"] #dor-chatbot-panel{right:auto;left:0}
#dor-chatbot-panel[hidden]{display:none}
#dor-chatbot-panel header{background:var(--dor-header);color:#fff;padding:13px 15px;display:flex;justify-content:space-between;align-items:center}
#dor-chatbot-close{background:none;border:0;color:#fff;font-size:25px;cursor:pointer}
#dor-chatbot-messages{padding:13px;overflow:auto;flex:1}
.dor-chat-message{white-space:pre-wrap;overflow-wrap:anywhere;margin:8px 0;padding:10px 12px;border-radius:10px;line-height:1.5}
.dor-chat-user{background:var(--dor-user);color:#fff;margin-left:30px}
.dor-chat-answer{background:var(--dor-bot);color:var(--dor-text);margin-right:15px}
.dor-chat-sources{font-size:12px;margin-top:8px}
.dor-chat-sources a{display:block;overflow-wrap:anywhere}
.dor-chatbot-input-row{display:flex;gap:7px;padding:10px;border-top:1px solid #ddd}
#dor-chatbot-question{min-width:0;flex:1;resize:vertical;padding:8px;border:1px solid #bbc4cf;border-radius:7px}
#dor-chatbot-send{border:0;border-radius:7px;background:#142d4e;color:#fff;padding:8px 11px;cursor:pointer}
#dor-chatbot-status{font-size:12px;padding:0 12px 9px}
.dor-chat-consent{display:flex;gap:8px;margin-top:9px}
.dor-chat-consent button{padding:7px 10px;border:1px solid #8795a5;border-radius:6px;background:#fff;cursor:pointer}
@media(max-width:480px){#dor-chatbot{bottom:12px}#dor-chatbot[data-position="bottom_left"]{left:10px}#dor-chatbot:not([data-position="bottom_left"]){right:10px}#dor-chatbot-panel{height:min(70vh,560px)}}
</style>

<script>
(function () {
    const root = document.getElementById('dor-chatbot');
    if (!root || root.dataset.initialized) return;
    root.dataset.initialized = '1';

    const $ = id => document.getElementById(id);
    const panel = $('dor-chatbot-panel');
    const messages = $('dor-chatbot-messages');
    const question = $('dor-chatbot-question');
    const status = $('dor-chatbot-status');
    const subdomain = root.dataset.subdomain;
    let pendingQuestion = '';

    root.style.setProperty('--dor-panel', root.dataset.panel);
    root.style.setProperty('--dor-header', root.dataset.header);
    root.style.setProperty('--dor-user', root.dataset.user);
    root.style.setProperty('--dor-bot', root.dataset.bot);
    root.style.setProperty('--dor-text', root.dataset.text);
    $('dor-chatbot-title').textContent = root.dataset.botName;

    function addMessage(text, type) {
        const item = document.createElement('div');
        item.className = 'dor-chat-message ' + type;
        item.textContent = text;
        messages.appendChild(item);
        messages.scrollTop = messages.scrollHeight;
        return item;
    }

    function addSources(parent, sources) {
        if (!Array.isArray(sources) || !sources.length) return;
        const area = document.createElement('div');
        area.className = 'dor-chat-sources';
        const heading = document.createElement('strong');
        heading.textContent = 'DoR Official Sources';
        area.appendChild(heading);

        sources.forEach(source => {
            if (!source.url) return;
            try {
                const url = new URL(source.url, window.location.origin);
                if (!['http:', 'https:'].includes(url.protocol)) return;
                const link = document.createElement('a');
                link.href = url.href;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.textContent = source.title || 'Official document';
                area.appendChild(link);
            } catch (_) {}
        });
        parent.appendChild(area);
    }

    async function post(path, payload) {
        const response = await fetch(path, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': root.dataset.token
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json().catch(() => ({}));
        return { response, data };
    }

    function consentButtons(questionText, parent) {
        const area = document.createElement('div');
        area.className = 'dor-chat-consent';

        const yes = document.createElement('button');
        yes.type = 'button';
        yes.textContent = 'Agree & Continue';
        yes.addEventListener('click', async () => {
            yes.disabled = true;
            no.disabled = true;
            status.textContent = 'बाह्य खोज सेवा जाँच हुँदैछ...';
            try {
                const result = await post(
                    '/chatbot/' + encodeURIComponent(subdomain) + '/external-consent',
                    { question: questionText }
                );
                status.textContent = result.data.message || 'बाह्य खोज उपलब्ध छैन।';
            } catch (_) {
                status.textContent = 'अहिले अनुरोध पूरा गर्न सकिएन।';
            }
        });

        const no = document.createElement('button');
        no.type = 'button';
        no.textContent = 'No, Thanks';
        no.addEventListener('click', () => {
            area.remove();
            status.textContent = 'DoR आधिकारिक स्रोतबाट मात्र उत्तर खोजिनेछ।';
        });

        area.append(yes, no);
        parent.appendChild(area);
    }

    async function sendQuestion() {
        const value = question.value.trim();
        if (value.length < 2) return;

        question.value = '';
        pendingQuestion = value;
        addMessage(value, 'dor-chat-user');
        status.textContent = 'आधिकारिक स्रोत खोजिँदैछ...';
        $('dor-chatbot-send').disabled = true;

        try {
            const result = await post(
                '/chatbot/' + encodeURIComponent(subdomain) + '/message',
                { question: value }
            );
            const data = result.data;

            if (data.needs_consent) {
                const item = addMessage(data.message, 'dor-chat-answer');
                consentButtons(value, item);
            } else if (data.ok) {
                const item = addMessage(data.answer || 'उत्तर उपलब्ध भएन।', 'dor-chat-answer');
                if (data.source_label) {
                    const label = document.createElement('small');
                    label.textContent = data.source_label;
                    item.appendChild(document.createElement('br'));
                    item.appendChild(label);
                }
                addSources(item, data.sources);
                if (data.provider_label) {
                    const provider = document.createElement('small');
                    provider.textContent = 'Powered by ' + data.provider_label;
                    item.appendChild(document.createElement('br'));
                    item.appendChild(provider);
                }
                status.textContent = '';
            } else {
                status.textContent = data.message || 'अहिले उत्तर उपलब्ध छैन।';
            }
        } catch (_) {
            status.textContent = 'सर्भरसँग सम्पर्क हुन सकेन। कृपया फेरि प्रयास गर्नुहोस्।';
        } finally {
            $('dor-chatbot-send').disabled = false;
        }
    }

    $('dor-chatbot-toggle').addEventListener('click', () => {
        panel.hidden = !panel.hidden;
        if (!panel.hidden && !messages.children.length) {
            addMessage(root.dataset.welcome, 'dor-chat-answer');
        }
    });
    $('dor-chatbot-close').addEventListener('click', () => panel.hidden = true);
    $('dor-chatbot-send').addEventListener('click', sendQuestion);
    question.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendQuestion();
        }
    });
})();
</script>
