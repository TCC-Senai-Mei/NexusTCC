<?php

namespace App\Services;

class ProtocolGeneratorService
{
    /**
     * Gera um número de protocolo aleatório e único no padrão: NL-2026-XXXXX
     */
    public function gerarProtocolo(): string
    {
        $aleatorio = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return "NL-2026-" . $aleatorio;
    }
}