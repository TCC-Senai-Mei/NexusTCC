<div class="flex flex-col mb-4" 
     :class="msg.sender_role === 'mei' ? 'items-end' : 'items-start'">
    
    <div class="max-w-[75%] p-3.5 rounded-2xl shadow-sm relative text-sm leading-relaxed"
         :class="msg.sender_role === 'mei' 
                ? 'bg-[#1A4B3D] text-white rounded-br-none' 
                : (msg.sender_role === 'bot' 
                    ? 'bg-[#D6F2A6] text-[#031617] rounded-bl-none border border-rgba(26,75,61,0.1)' 
                    : 'bg-white text-[#031617] rounded-bl-none border border-gray-200')">
        
        <p class="whitespace-pre-line" x-text="msg.message"></p>

        <template x-if="msg.protocol_number">
            <div class="mt-2.5 pt-2 border-t flex items-center gap-1.5 text-xs font-semibold"
                 :class="msg.sender_role === 'mei' ? 'border-[#266251] text-[#D6F2A6]' : 'border-[#b8da84] text-[#1A4B3D]'">
                <i class="bi bi-ticket-perforated"></i>
                <span>Protocolo: <span x-text="msg.protocol_number"></span></span>
            </div>
        </template>
    </div>

    <span class="text-[10px] text-[#4a6b5a] mt-1 px-1" 
          x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})">
    </span>
</div>