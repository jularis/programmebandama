@extends('admin.layouts.app')
@section('panel')

<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="card b-radius--10" style="height: calc(100vh - 180px); display: flex; flex-direction: column;">
            {{-- Header --}}
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="ai-avatar">
                        <i class="las la-robot" style="font-size: 28px; color: #6c5ce7;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Assistant IA — Programme Bandama</h6>
                        <small class="text-muted">Posez vos questions sur les données de l'application</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline--danger" id="clearBtn" title="Effacer la conversation">
                    <i class="las la-trash"></i> Effacer
                </button>
            </div>

            {{-- Messages --}}
            <div class="card-body p-3 overflow-auto flex-grow-1" id="messagesContainer">
                {{-- Welcome message --}}
                <div class="d-flex gap-2 mb-3" id="welcomeMsg">
                    <div class="flex-shrink-0">
                        <div class="ai-bubble-icon"><i class="las la-robot"></i></div>
                    </div>
                    <div class="ai-bubble p-3">
                        <p class="mb-1">Bonjour ! Je suis votre assistant IA pour le <strong>Programme Bandama</strong>.</p>
                        <p class="mb-1">Je peux vous aider à :</p>
                        <ul class="mb-1 ps-3">
                            <li>Consulter les statistiques globales</li>
                            <li>Rechercher des producteurs, parcelles ou coopératives</li>
                            <li>Analyser les livraisons et stocks</li>
                            <li>Obtenir des informations sur les formations</li>
                        </ul>
                        <p class="mb-0 text-muted" style="font-size:0.85em;">Exemple : <em>"Combien de producteurs au total ?"</em> ou <em>"Liste les 5 dernières livraisons"</em></p>
                    </div>
                </div>
            </div>

            {{-- Typing indicator --}}
            <div id="typingIndicator" class="px-3 pb-1" style="display:none;">
                <div class="d-flex gap-2 align-items-center">
                    <div class="ai-bubble-icon"><i class="las la-robot"></i></div>
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="card-footer border-top p-3">
                <form id="chatForm" class="d-flex gap-2">
                    @csrf
                    <input
                        type="text"
                        id="messageInput"
                        class="form-control"
                        placeholder="Posez votre question ici..."
                        autocomplete="off"
                        maxlength="2000"
                    />
                    <button type="submit" class="btn btn--primary px-4" id="sendBtn">
                        <i class="las la-paper-plane"></i>
                    </button>
                </form>
                <div class="mt-1 text-end">
                    <small class="text-muted" id="charCount">0 / 2000</small>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('style')
<style>
    /* AI avatar bubble */
    .ai-bubble-icon {
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* AI response bubble */
    .ai-bubble {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0 12px 12px 12px;
        max-width: 80%;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    /* User message bubble */
    .user-bubble {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        color: #fff;
        border-radius: 12px 12px 0 12px;
        padding: 10px 14px;
        max-width: 75%;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    /* Markdown-like formatting in AI bubbles */
    .ai-bubble strong { color: #2d3436; }
    .ai-bubble ul, .ai-bubble ol { margin-bottom: 0.5rem; }
    .ai-bubble li { margin-bottom: 2px; }
    .ai-bubble table { font-size: 0.85rem; width: 100%; border-collapse: collapse; margin-top: 6px; }
    .ai-bubble table th, .ai-bubble table td { border: 1px solid #dee2e6; padding: 4px 8px; }
    .ai-bubble table th { background: #e9ecef; }
    .ai-bubble code { background: #e9ecef; padding: 1px 5px; border-radius: 3px; font-size: 0.85em; color: #d63384; }
    .ai-bubble pre { background: #272822; color: #f8f8f2; padding: 10px; border-radius: 6px; overflow-x: auto; font-size: 0.82rem; }

    /* Typing dots animation */
    .typing-dots {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0 12px 12px 12px;
        padding: 10px 14px;
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .typing-dots span {
        width: 7px; height: 7px;
        background: #adb5bd;
        border-radius: 50%;
        animation: typing 1.2s infinite;
    }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-5px); opacity: 1; }
    }

    /* Scrollbar */
    #messagesContainer::-webkit-scrollbar { width: 5px; }
    #messagesContainer::-webkit-scrollbar-track { background: #f1f1f1; }
    #messagesContainer::-webkit-scrollbar-thumb { background: #c0c0c0; border-radius: 10px; }
</style>
@endpush

@push('script')
<script>
(function () {
    const form = document.getElementById('chatForm');
    const input = document.getElementById('messageInput');
    const container = document.getElementById('messagesContainer');
    const sendBtn = document.getElementById('sendBtn');
    const typingIndicator = document.getElementById('typingIndicator');
    const clearBtn = document.getElementById('clearBtn');
    const charCount = document.getElementById('charCount');

    let conversationHistory = [];

    // ── Char counter ──────────────────────────────
    input.addEventListener('input', () => {
        charCount.textContent = `${input.value.length} / 2000`;
    });

    // ── Submit ────────────────────────────────────
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        appendUserMessage(message);
        input.value = '';
        charCount.textContent = '0 / 2000';
        setLoading(true);

        try {
            const res = await fetch('{{ route("admin.aichat.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message, history: conversationHistory }),
            });

            const data = await res.json();

            if (data.success) {
                conversationHistory = data.history;
                appendAiMessage(data.message);
            } else {
                appendAiMessage('❌ ' + (data.message || 'Une erreur est survenue.'), true);
            }
        } catch (err) {
            appendAiMessage('❌ Impossible de contacter le serveur. Vérifiez votre connexion.', true);
        } finally {
            setLoading(false);
        }
    });

    // ── Clear conversation ─────────────────────────
    clearBtn.addEventListener('click', () => {
        if (!confirm('Effacer la conversation ?')) return;
        conversationHistory = [];
        // Remove all messages except welcome
        const msgs = container.querySelectorAll('.chat-message');
        msgs.forEach(m => m.remove());
    });

    // ── Helpers ───────────────────────────────────
    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-end mb-3 chat-message';
        div.innerHTML = `
            <div class="user-bubble">${escapeHtml(text)}</div>
        `;
        container.appendChild(div);
        scrollBottom();
    }

    function appendAiMessage(text, isError = false) {
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-3 chat-message';
        const content = isError ? `<span class="text-danger">${escapeHtml(text)}</span>` : markdownToHtml(text);
        div.innerHTML = `
            <div class="flex-shrink-0">
                <div class="ai-bubble-icon"><i class="las la-robot"></i></div>
            </div>
            <div class="ai-bubble p-3">${content}</div>
        `;
        container.appendChild(div);
        scrollBottom();
    }

    function setLoading(loading) {
        sendBtn.disabled = loading;
        input.disabled = loading;
        typingIndicator.style.display = loading ? 'block' : 'none';
        if (loading) scrollBottom();
    }

    function scrollBottom() {
        setTimeout(() => { container.scrollTop = container.scrollHeight; }, 50);
    }

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Minimal Markdown → HTML (bold, italic, code, lists, headers)
    function markdownToHtml(text) {
        let html = escapeHtml(text);

        // Code blocks
        html = html.replace(/```[\w]*\n?([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        // Inline code
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        // Bold
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        // Italic
        html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        // Headers
        html = html.replace(/^### (.+)$/gm, '<h6 class="mt-2 mb-1 fw-bold">$1</h6>');
        html = html.replace(/^## (.+)$/gm, '<h5 class="mt-2 mb-1 fw-bold">$1</h5>');
        html = html.replace(/^# (.+)$/gm, '<h4 class="mt-2 mb-1 fw-bold">$1</h4>');
        // Unordered lists
        html = html.replace(/^[-*] (.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/s, (match) => `<ul class="ps-3">${match}</ul>`);
        // Numbered lists
        html = html.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');
        // Line breaks
        html = html.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
        html = '<p>' + html + '</p>';
        // Clean up empty paragraphs
        html = html.replace(/<p>\s*<\/p>/g, '');

        return html;
    }

    // Allow Enter to submit, Shift+Enter for newline
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });
})();
</script>
@endpush
