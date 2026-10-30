<?php

/*
|--------------------------------------------------------------------------
| Dados institucionais oficiais da CBN-GO
|--------------------------------------------------------------------------
|
| Fallback: os valores oficiais são mantidos no painel (Sistema → Dados
| institucionais) e lidos por App\Support\InstitutionalInfo (Issue #37).
| Estes valores só aparecem se o registro do painel não existir ou o banco
| estiver indisponível. As chaves espelham a tabela institutional_settings.
|
*/

return [

    'contact' => [
        'email' => env('CBNGO_EMAIL', 'contato@cbngo.org.br'),
        'phone' => env('CBNGO_PHONE', '(62) 8487-6863'),
        'whatsapp' => env('CBNGO_WHATSAPP', '(62) 8487-6863'),
        'office_hours' => env('CBNGO_OFFICE_HOURS', 'Segunda a sexta, das 9h às 18h30'),
        // Endereço da sede conforme o Art. 2º do Estatuto.
        'street' => 'Rua 04',
        'number' => '515',
        'complement' => '12º andar, sala 1.207, Edifício Parthenon Center',
        'district' => 'Setor Central',
        'city' => 'Goiânia',
        'state' => 'GO',
        'postal_code' => '74020-045',
        'maps_url' => null,
    ],

    'social' => [
        'instagram' => 'https://www.instagram.com/cbn_go/',
        'facebook' => 'https://www.facebook.com/cbnacionalgoias',
        'youtube' => 'https://youtube.com/@cbngoias',
        'tiktok' => null,
        'spotify' => null,
        'linkedin' => null,
    ],

    // A chave do cache muda a cada edição no painel (updated_at); o TTL só limita o tamanho do cache.
    'institutional' => [
        'cache_ttl' => (int) env('INSTITUTIONAL_CACHE_TTL', 3600),
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

    /*
    |--------------------------------------------------------------------------
    | Projetos sociais (Issue #27)
    |--------------------------------------------------------------------------
    |
    | As categorias seguem a missão integral do Pacto de Lausanne. Enquanto o site
    | não tiver um artigo próprio sobre o Pacto, o link aponta para o texto oficial.
    |
    */

    'lausanne_url' => env('CBNGO_LAUSANNE_URL', 'https://lausanne.org/pt-br/statement/pacto-de-lausanne'),

    /*
    |--------------------------------------------------------------------------
    | Estatuto em Akoma Ntoso 3.0 (Issue #33)
    |--------------------------------------------------------------------------
    |
    | O HTML do estatuto é gerado do XML e guardado em cache. A chave muda a cada
    | edição no painel (updated_at); o TTL só limita o tamanho do cache.
    |
    */

    'statute' => [
        'cache_ttl' => (int) env('STATUTE_CACHE_TTL', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | Banners do hero da home (Issue #23)
    |--------------------------------------------------------------------------
    |
    | A chave do cache muda quando um banner é criado, editado ou removido no
    | painel; o TTL limita o atraso de banners cuja vigência começa ou termina.
    |
    */

    'home' => [
        'banners_cache_ttl' => (int) env('HOME_BANNERS_CACHE_TTL', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Área das Igrejas / Painel Administrativo
    |--------------------------------------------------------------------------
    |
    | Endereço do painel administrativo da CBN-GO (gestão e área das igrejas).
    |
    */

    'panel_url' => env('CBNGO_PANEL_URL', 'https://painel.cbngo.org.br'),

];
