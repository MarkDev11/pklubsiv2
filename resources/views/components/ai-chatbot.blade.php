{{-- AI Chatbot Floating Widget --}}
<div x-data="aiChat()" x-cloak class="fixed bottom-6 right-6 z-50">

    {{-- Chat Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-4 w-80 sm:w-96 bg-white dark:bg-surface-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-surface-700 overflow-hidden flex flex-col"
         style="height: 480px;">

        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">AI Asisten PKL</h3>
                    <p class="text-xs text-white/70">Tanya apapun tentang PKL</p>
                </div>
            </div>
            <button @click="open = false" class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="chatMessages">
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? 'bg-primary-600 text-white rounded-2xl rounded-br-md text-sm leading-relaxed px-4 py-2.5 max-w-[85%]'
                        : 'bg-gray-100 dark:bg-surface-700 text-gray-800 dark:text-gray-200 rounded-2xl rounded-bl-md text-sm leading-relaxed px-5 py-3 max-w-[90%] ai-message-content'"
                         class="prose prose-sm dark:prose-invert">
                        <template x-if="msg.role === 'user'">
                            <span x-text="msg.content"></span>
                        </template>
                        <template x-if="msg.role === 'assistant'">
                            <div x-html="parseMarkdown(msg.content)"></div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="loading" class="flex justify-start">
                <div class="bg-gray-100 dark:bg-surface-700 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex gap-1.5 items-center h-4">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="p-3 border-t border-gray-200 dark:border-surface-700 bg-gray-50/50 dark:bg-surface-800/50">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input x-model="input" type="text" placeholder="Ketik pertanyaan..."
                       class="flex-1 form-input text-sm rounded-xl py-2.5 bg-white dark:bg-surface-900 border-gray-200 dark:border-surface-600 focus:ring-primary-500 focus:border-primary-500" :disabled="loading">
                <button type="submit" :disabled="loading || !input.trim()"
                        class="px-3.5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors disabled:opacity-50">
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Floating Button --}}
    <button @click="open = !open"
            class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 text-white rounded-full shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-1 hover:shadow-primary-500/40 transition-all duration-300 flex items-center justify-center relative group">
        <div class="absolute inset-0 rounded-full bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

{{-- Add marked.js for markdown parsing --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
/* CSS overrides for AI Markdown so it looks neat and professional */
.ai-message-content p { margin-bottom: 0.5em; }
.ai-message-content p:last-child { margin-bottom: 0; }
.ai-message-content strong { font-weight: 700; color: #111827; }
.dark .ai-message-content strong { color: #f9fafb; }
.ai-message-content ul { list-style-type: disc; margin-left: 1.25em; margin-bottom: 0.5em; }
.ai-message-content ol { list-style-type: decimal; margin-left: 1.25em; margin-bottom: 0.5em; }
.ai-message-content li { margin-bottom: 0.25em; }
</style>

<script>
function aiChat() {
    return {
        open: false,
        loading: false,
        input: '',
        messages: [
            { role: 'assistant', content: 'Halo! Saya asisten pintar PKL UBSI. Ada yang ingin Anda tanyakan?' }
        ],
        parseMarkdown(text) {
            if (typeof marked !== 'undefined') {
                return marked.parse(text);
            }
            return text;
        },
        async sendMessage() {
            if (!this.input.trim() || this.loading) return;

            const userMsg = this.input.trim();
            this.messages.push({ role: 'user', content: userMsg });
            this.input = '';
            this.loading = true;

            this.$nextTick(() => {
                this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
            });

            try {
                const res = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        message: userMsg,
                        history: this.messages.slice(-10),
                    }),
                });
                const data = await res.json();
                this.messages.push({ role: 'assistant', content: data.reply || 'Maaf, tidak ada respons.' });
            } catch (err) {
                this.messages.push({ role: 'assistant', content: 'Maaf, terjadi kesalahan saat menyambungkan dengan otak AI.' });
            }

            this.loading = false;
            this.$nextTick(() => {
                this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
            });
        }
    }
}
</script>
