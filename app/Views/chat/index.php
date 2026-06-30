<?php
$userId   = session()->get('user_id');
$userName = session()->get('user_name');
$userRole = session()->get('user_role') ?? 'mei';
$isMei    = $userRole === 'mei';
?>

<link rel="stylesheet" href="<?= base_url('css/chatbot.css') ?>">

<div class="flex rounded-2xl overflow-hidden border bg-white animate-fadeIn"
     style="border-color:rgba(26,75,61,.1); height:calc(100vh - 8.5rem)"
     x-data="chatbotApp()" x-init="init()">

    <div class="w-80 flex-shrink-0 flex flex-col border-r bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.1)">
        <div class="flex items-center justify-between px-4 py-4 border-b bg-white" style="border-color:rgba(26,75,61,.1)">
            <p class="font-bold text-sm text-[#031617] flex items-center gap-2">
                <i class="bi bi-chat-right-text text-[#1A4B3D]"></i> Atendimentos
            </p>
            <?php if ($isMei): ?>
                <button @click="novaSessao()" 
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-[#1A4B3D] text-white hover:opacity-90 transition-opacity flex items-center gap-1 shadow-sm active:scale-95">
                    <i class="bi bi-plus-lg"></i> Novo
                </button>
            <?php endif; ?>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            <template x-for="sessao in sessoes" :key="sessao.id">
                <div @click="selecionarSessao(sessao.id)"
                     :class="idSessaoAtiva === sessao.id ? 'bg-[#f0f9f0] border-[#1A4B3D]/20 text-[#1A4B3D]' : 'bg-white hover:bg-gray-50 text-[#031617]'"
                     class="p-3 rounded-xl border border-gray-100 cursor-pointer transition-all shadow-sm relative animate-fadeIn">
                    
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-xs truncate max-w-[140px]" x-text="sessao.titulo"></span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase"
                              :class="sessao.tipo === 'bot' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600'"
                              x-text="sessao.tipo"></span>
                    </div>

                    <p class="text-xs text-[#4a6b5a] truncate pr-5" x-text="sessao.last_message || 'Nenhuma mensagem disponível'"></p>

                    <template x-if="parseInt(sessao.unread) > 0">
                        <span class="absolute bottom-3 right-3 w-4 h-4 rounded-full bg-[#1A4B3D] text-white flex items-center justify-center text-[9px] font-bold" 
                              x-text="sessao.unread"></span>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <div class="flex-1 flex flex-col bg-gray-50">
        
        <template x-if="!idSessaoAtiva">
            <div class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-white">
                <div class="w-16 h-16 rounded-full bg-[#f0f9f0] text-[#1A4B3D] flex items-center justify-center text-2xl mb-4 shadow-inner">
                    <i class="bi bi-chat-left-dots-fill"></i>
                </div>
                <h3 class="text-base font-bold text-[#031617]">Nenhum atendimento selecionado</h3>
                <p class="text-xs text-[#4a6b5a] max-w-xs mt-1">Selecione uma conversa na barra lateral esquerda ou inicie um novo contato com o assistente virtual.</p>
            </div>
        </template>

        <template x-if="idSessaoAtiva">
            <div class="flex-1 flex flex-col min-h-0 bg-white animate-fadeIn">
                
                <div class="px-5 py-3.5 border-b bg-white flex items-center justify-between shadow-sm" style="border-color:rgba(26,75,61,.1)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 badge-pulse"></div>
                        <div>
                            <h4 class="font-bold text-sm text-[#031617]" x-text="sessaoAtivaObjeto()?.titulo"></h4>
                            <p class="text-[11px] text-[#4a6b5a] flex items-center gap-1">
                                <i class="bi bi-shield-check"></i> Protocolo: <span x-text="sessaoAtivaObjeto()?.protocol_number || 'Não gerado'"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-5 bg-[#fcfdfc]" id="messageContainer">
                    <template x-for="(msg, index) in mensagens" :key="msg.id">
                        <div class="animate-fadeIn">
                            <?= view('components/message-bubble') ?>
                            
                            <template x-if="msg.opcoes && msg.opcoes.length > 0 && index === mensagens.length - 1">
                                <?= view('components/quick-replies') ?>
                            </template>
                        </div>
                    </template>
                    
                    <div x-show="digitando" class="flex gap-1.5 p-3 bg-gray-100 rounded-2xl w-fit rounded-bl-none text-xs text-gray-500 mb-4 animate-pulse">
                        <span class="font-medium text-[#1A4B3D]">O Assistente está processando</span>
                        <span class="flex gap-0.5 items-center"><span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce"></span></span>
                    </div>
                </div>

                <div class="p-3.5 border-t bg-gray-50" style="border-color:rgba(26,75,61,.08)">
                    <form @submit.prevent="enviarMensagemTexto()" class="flex gap-2 bg-white p-1 rounded-xl border border-gray-200 shadow-sm focus-within:border-[#1A4B3D] transition-colors">
                        <input type="text" x-model="inputTexto" placeholder="Digite uma dúvida ou escolha uma opção acima..."
                               class="flex-1 bg-transparent px-3 py-2 text-sm focus:outline-none text-[#031617]"
                               :disabled="digitando" />
                        <button type="submit" :disabled="!inputTexto.trim() || digitando"
                                class="px-4 py-2 bg-[#1A4B3D] text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity disabled:opacity-40 flex items-center gap-1 shadow-sm active:scale-95">
                            <span>Enviar</span> <i class="bi bi-send-fill text-[10px]"></i>
                        </button>
                    </form>
                </div>

            </div>
        </template>
    </div>
</div>

<script src="<?= base_url('js/chatbot.js') ?>"></script>