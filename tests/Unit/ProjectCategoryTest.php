<?php

namespace Tests\Unit;

use App\Enums\ProjectCategory;
use PHPUnit\Framework\TestCase;

class ProjectCategoryTest extends TestCase
{
    /**
     * Valida os slugs sem acento usados no filtro ?categoria= e a conversão de volta.
     */
    public function test_slug_round_trips_and_unknown_slug_returns_null(): void
    {
        $this->assertSame(['social', 'cultural', 'educacao', 'lideranca'], array_map(fn (ProjectCategory $c) => $c->slug(), ProjectCategory::cases()));

        foreach (ProjectCategory::cases() as $category) {
            $this->assertSame($category, ProjectCategory::fromSlug($category->slug()));
        }

        $this->assertNull(ProjectCategory::fromSlug('Educação'));
        $this->assertNull(ProjectCategory::fromSlug(''));
        $this->assertNull(ProjectCategory::fromSlug(null));
    }

    /**
     * Valida que o valor persistido é o rótulo do painel e as referências ao Pacto de Lausanne.
     */
    public function test_values_and_lausanne_references_mirror_admin_panel(): void
    {
        $this->assertSame(['Social', 'Cultural', 'Educação', 'Liderança'], array_map(fn (ProjectCategory $c) => $c->value, ProjectCategory::cases()));
        $this->assertSame('Pacto de Lausanne, art. 5: Responsabilidade social cristã', ProjectCategory::SOCIAL->lausanneReference());
        $this->assertSame('Pacto de Lausanne, art. 10: Evangelização e cultura', ProjectCategory::CULTURAL->lausanneReference());
        $this->assertSame('Pacto de Lausanne, art. 11: Educação e liderança', ProjectCategory::EDUCACAO->lausanneReference());
        $this->assertSame('Pacto de Lausanne, art. 11: Educação e liderança', ProjectCategory::LIDERANCA->lausanneReference());
        $this->assertStringContainsString('text-white', ProjectCategory::SOCIAL->badgeClasses());
    }
}
