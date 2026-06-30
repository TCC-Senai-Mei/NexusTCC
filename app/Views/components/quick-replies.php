<div class="flex flex-wrap gap-2 mt-2 mb-4 justify-start">
    <template x-for="opcao in msg.opcoes" :key="opcao.id">
        <button @click="enviarOpcao(opcao.id, opcao.label)"
                class="px-4 py-2 rounded-xl text-xs font-medium border bg-white text-[#1A4B3D] border-[#1A4B3D]/20 hover:bg-[#f0f9f0] hover:border-[#1A4B3D] transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
            <i class="bi bi-chat-left-dots text-[#4a6b5a]"></i>
            <span x-text="opcao.label"></span>
        </button>
    </template>
</div>