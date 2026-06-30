<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => \App\Filters\AuthFilter::class,
    ];

    /**
     * List of always-active filters
     */
    public array $required = [
        'before' => [],
        'after'  => [
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [],
        'after'  => [],
    ];

    // CSRF desativado para rotas /api/* (chamadas AJAX do chatbot e demais)
    public array $methods = [];

    public array $filters = [
        // 'csrf' => ['except' => ['api/*']], // descomente se precisar de CSRF global
    ];
}
