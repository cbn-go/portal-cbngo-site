<?php

namespace Tests;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Cria um perfil de autor como o painel: o nome de exibição e o status ficam em `users`,
     * o restante em `author_profiles`.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function createAuthor(array $attributes = []): Author
    {
        $userAttributes = array_intersect_key($attributes, array_flip(['name', 'is_active']));

        $user = User::factory()->author()->create($userAttributes);

        return Author::factory()->create([
            ...array_diff_key($attributes, $userAttributes),
            'user_id' => $user->id,
        ]);
    }
}
