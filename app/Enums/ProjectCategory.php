<?php

namespace App\Enums;

/**
 * Categorias dos projetos sociais, espelho de App\Enums\ProjectCategory do painel administrativo.
 *
 * Seguem as dimensões da missão integral do Pacto de Lausanne. O valor persistido é o rótulo exibido;
 * o slug (sem acento) é usado no filtro ?categoria= da listagem pública.
 */
enum ProjectCategory: string
{
    case SOCIAL = 'Social';
    case CULTURAL = 'Cultural';
    case EDUCACAO = 'Educação';
    case LIDERANCA = 'Liderança';

    /**
     * Identificador sem acento para URLs.
     */
    public function slug(): string
    {
        return match ($this) {
            self::SOCIAL => 'social',
            self::CULTURAL => 'cultural',
            self::EDUCACAO => 'educacao',
            self::LIDERANCA => 'lideranca',
        };
    }

    /**
     * Categoria a partir do slug da URL; slug desconhecido retorna null.
     */
    public static function fromSlug(?string $slug): ?self
    {
        foreach (self::cases() as $category) {
            if ($category->slug() === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Descrição curta da categoria (mesmo texto do painel).
     */
    public function description(): string
    {
        return match ($this) {
            self::SOCIAL => 'Ações de cuidado, justiça e dignidade junto a pessoas em vulnerabilidade.',
            self::CULTURAL => 'Expressões culturais e artísticas como ponte para o Evangelho na comunidade.',
            self::EDUCACAO => 'Reforço escolar, cursos, alfabetização e formação para a vida.',
            self::LIDERANCA => 'Formação e capacitação de líderes para a igreja e a sociedade.',
        };
    }

    /**
     * Artigo do Pacto de Lausanne que fundamenta a categoria (mesmo texto do painel).
     */
    public function lausanneReference(): string
    {
        return match ($this) {
            self::SOCIAL => 'Pacto de Lausanne, art. 5: Responsabilidade social cristã',
            self::CULTURAL => 'Pacto de Lausanne, art. 10: Evangelização e cultura',
            self::EDUCACAO, self::LIDERANCA => 'Pacto de Lausanne, art. 11: Educação e liderança',
        };
    }

    /**
     * Classes do badge da categoria (texto branco sobre tons escuros, contraste AA).
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::SOCIAL => 'bg-rose-700 text-white',
            self::CULTURAL => 'bg-amber-800 text-white',
            self::EDUCACAO => 'bg-sky-800 text-white',
            self::LIDERANCA => 'bg-emerald-800 text-white',
        };
    }
}
