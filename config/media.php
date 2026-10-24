<?php

/*
|--------------------------------------------------------------------------
| Mídia compartilhada com o painel administrativo (Issue #19)
|--------------------------------------------------------------------------
|
| O banco guarda caminhos relativos (ex.: news/featured/abc.jpg). O portal
| monta a URL final como MEDIA_URL/{caminho}. Em produção, MEDIA_URL é
| https://midia.cbngo.org.br (veja docs/integracao-portal-publico.md do painel).
|
*/

return [

    'url' => env('MEDIA_URL', 'http://localhost:8000/storage'),

];
