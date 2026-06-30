function chatbotApp() {
    return {
        sessoes: [],
        mensagens: [],
        idSessaoAtiva: null,
        inputTexto: '',
        digitando: false,
        // Alinhado exatamente ao nome da sua pasta dentro de htdocs:
        baseUrl: '/NexusTCC/public/index.php', 

        async init() {
            await this.carregarSessoes();
            // Abre o primeiro atendimento automaticamente se houver histórico
            if (this.sessoes.length > 0) {
                this.selecionarSessao(this.sessoes[0].id);
            }
        },

        async carregarSessoes() {
            try {
                const response = await fetch(`${this.baseUrl}/chat/listarSessoes`);
                if (response.ok) {
                    this.sessoes = await response.json();
                }
            } catch (error) {
                console.error("Erro ao listar atendimentos:", error);
            }
        },

        async selecionarSessao(id) {
            this.idSessaoAtiva = id;
            this.mensagens = [];
            this.digitando = false;
            
            // Zera o contador de não lidas localmente para dar fluidez visual
            const sessao = this.sessoes.find(s => s.id === id);
            if (sessao) sessao.unread = 0;

            await this.carregarMensagens();
        },

        sessaoAtivaObjeto() {
            return this.sessoes.find(s => s.id === this.idSessaoAtiva) || null;
        },

        async carregarMensagens() {
            if (!this.idSessaoAtiva) return;
            try {
                const response = await fetch(`${this.baseUrl}/chat/carregarMensagens/${this.idSessaoAtiva}`);
                if (response.ok) {
                    this.mensagens = await response.json();
                    this.scrollParaOFinal();
                }
            } catch (error) {
                console.error("Erro ao carregar mensagens:", error);
            }
        },

        async novaSessao() {
            try {
                const response = await fetch(`${this.baseUrl}/chat/criarSessao`, { method: 'POST' });
                if (response.ok) {
                    const data = await response.json();
                    if (data.session_id) {
                        await this.carregarSessoes();
                        this.selecionarSessao(data.session_id);
                    }
                }
            } catch (error) {
                console.error("Erro ao iniciar nova conversa:", error);
            }
        },

        async enviarMensagemTexto() {
            const texto = this.inputTexto.trim();
            if (!texto || !this.idSessaoAtiva) return;

            this.inputTexto = '';
            
            // Otimismo Visual: renderiza o balão do usuário na hora antes de esperar a resposta da API
            this.mensagens.push({
                id: Date.now(),
                sender_role: 'mei',
                message: texto,
                created_at: new Date().toISOString(),
                opcoes: []
            });
            this.scrollParaOFinal();
            this.digitando = true;

            await this.dispararApiEnviar({ session_id: this.idSessaoAtiva, message: texto });
        },

        async enviarOpcao(opcaoId, labelOpcao) {
            if (!this.idSessaoAtiva) return;

            this.mensagens.push({
                id: Date.now(),
                sender_role: 'mei',
                message: labelOpcao,
                created_at: new Date().toISOString(),
                opcoes: []
            });
            this.scrollParaOFinal();
            this.digitando = true;

            await this.dispararApiEnviar({ session_id: this.idSessaoAtiva, message: labelOpcao, opcao_id: opcaoId });
        },

        async dispararApiEnviar(payload) {
            try {
                const response = await fetch(`${this.baseUrl}/chat/enviarMensagem`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    await this.carregarMensagens();
                    await this.carregarSessoes();
                }
            } catch (error) {
                console.error("Erro ao enviar dados para o chatbot:", error);
            } finally {
                this.digitando = false;
                this.scrollParaOFinal();
            }
        },

        scrollParaOFinal() {
            this.$nextTick(() => {
                const container = document.getElementById('messageContainer');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    };
}