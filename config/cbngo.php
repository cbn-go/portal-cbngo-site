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

];
