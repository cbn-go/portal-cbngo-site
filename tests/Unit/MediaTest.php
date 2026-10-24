<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Support\Html;
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

    /**
     * Valida que imagens com src relativo no HTML do RichEditor são convertidas para MEDIA_URL antes da sanitização.
     */
    public function test_html_sanitize_rewrites_relative_img_src_to_media_url_and_blocks_unsafe_src(): void
    {
        config(['media.url' => 'https://midia.cbngo.org.br']);

        $html = '<p><img src="news/attachments/foto.jpg" alt="Foto">'
            .'<img src="/news/attachments/outra.jpg">'
            .'<img src="https://cdn.example.com/externa.jpg">'
            .'<img src="javascript:alert(1)"></p>';

        $sanitized = Html::sanitize($html);

        $this->assertStringContainsString('src="https://midia.cbngo.org.br/news/attachments/foto.jpg"', $sanitized);
        $this->assertStringContainsString('src="https://midia.cbngo.org.br/news/attachments/outra.jpg"', $sanitized);
        $this->assertStringContainsString('src="https://cdn.example.com/externa.jpg"', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
    }

    /**
     * Valida que Author::getSafeSocialLinks() exige e-mail válido em links mailto:.
     */
    public function test_author_safe_social_links_validates_mailto_email(): void
    {
        $author = new Author([
            'social_links' => [
                'email_valido' => 'mailto:pastor@cbngo.org.br',
                'email_puro' => 'contato@cbngo.org.br',
                'mailto_invalido' => 'mailto:nao-e-um-email',
                'mailto_xss' => 'mailto:invalido"><script>alert(1)</script>',
                'arroba_invalido' => 'invalido@@dominio..com',
                'instagram' => 'https://instagram.com/cbngo',
            ],
        ]);

        $this->assertSame([
            'email_valido' => 'mailto:pastor@cbngo.org.br',
            'email_puro' => 'mailto:contato@cbngo.org.br',
            'instagram' => 'https://instagram.com/cbngo',
        ], $author->getSafeSocialLinks());
    }
}
