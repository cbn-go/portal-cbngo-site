<?php

/*
|--------------------------------------------------------------------------
| Dados institucionais oficiais da CBN-GO
|--------------------------------------------------------------------------
|
| Fonte única dos dados de contato e redes sociais exibidos em /contato,
| no rodapé e na barra superior do layout (Issue #7).
|
*/

return [

    'contact' => [
        // TODO revisar com secretaria: endereço completo da sede estadual.
        'address' => env('CBNGO_ADDRESS', 'Sede Administrativa CBN-GO, Goiânia - GO'),
        'phone' => env('CBNGO_PHONE', '(62) 8487-6863'),
        // TODO revisar com secretaria: número oficial de WhatsApp.
        'whatsapp' => env('CBNGO_WHATSAPP', '(62) 8487-6863'),
        'email' => env('CBNGO_EMAIL', 'contato@cbngo.org.br'),
        // TODO revisar com secretaria: site legado informa 9h às 18h30; rodapé anterior informava 8h às 17h.
        'office_hours' => env('CBNGO_OFFICE_HOURS', 'Segunda a sexta, das 9h às 18h30'),
    ],

    'social' => [
        'instagram' => 'https://www.instagram.com/cbn_go/',
        'facebook' => 'https://www.facebook.com/cbnacionalgoias',
        'youtube' => 'https://youtube.com/@cbngoias',
    ],

    /*
    |--------------------------------------------------------------------------
    | Identidade e SEO (Issue #8)
    |--------------------------------------------------------------------------
    |
    | Usados pelas metatags (<x-seo>) e pelos dados estruturados Schema.org.
    |
    */

    'organization' => [
        'name' => 'Convenção Batista Nacional do Estado de Goiás',
        'short_name' => 'CBN Goiás',
    ],

    'seo' => [
        'default_description' => 'Portal oficial da Convenção Batista Nacional do Estado de Goiás: notícias, artigos pastorais, diretório de igrejas e informações institucionais da CBN-GO.',
        // TODO substituir por arte própria de compartilhamento (1200×630).
        'default_image' => 'images/brand/cbn-go.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap (Issue #19)
    |--------------------------------------------------------------------------
    |
    | O conteúdo é publicado pelo painel, que o portal não observa. Um TTL curto
    | limita o atraso até o conteúdo novo (ou agendado que venceu) entrar no sitemap.
    |
    */

    'sitemap' => [
        'cache_ttl' => (int) env('SITEMAP_CACHE_TTL', 600),
    ],

];
