<?php

namespace Tests\Unit;

use App\Support\Media;
use Tests\TestCase;

class MediaTest extends TestCase
{
    /**
     * Valida que caminhos relativos salvos pelo painel viram URLs no storage de mídia (MEDIA_URL).
     */
    public function test_relative_path_is_prefixed_with_media_url(): void
    {
        config(['media.url' => 'https://midia.cbngo.org.br/']);

        $this->assertSame('https://midia.cbngo.org.br/news/featured/abc.jpg', Media::url('news/featured/abc.jpg'));
        $this->assertSame('https://midia.cbngo.org.br/news/featured/abc.jpg', Media::url('/news/featured/abc.jpg'));
    }

    /**
     * Valida que URLs absolutas http(s) são mantidas.
     */
    public function test_absolute_http_url_is_kept(): void
    {
        $this->assertSame('https://cdn.example.com/a.jpg', Media::url('https://cdn.example.com/a.jpg'));
        $this->assertSame('http://cdn.example.com/a.jpg', Media::url(' http://cdn.example.com/a.jpg '));
    }

    /**
     * Valida que valores vazios ou com esquemas inseguros não geram URL.
     */
    public function test_empty_or_unsafe_values_return_null(): void
    {
        $this->assertNull(Media::url(null));
        $this->assertNull(Media::url(''));
        $this->assertNull(Media::url('   '));
        $this->assertNull(Media::url('javascript:alert(1)'));
        $this->assertNull(Media::url('data:image/png;base64,AAAA'));
        $this->assertNull(Media::url('//evil.example.com/a.jpg'));
    }
}
