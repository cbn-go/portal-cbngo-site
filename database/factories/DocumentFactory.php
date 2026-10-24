<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Documento '.$this->faker->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->sentence(),
            'category' => 'outros',
            'file_url' => 'https://cbngo.com.br/dados-abertos/'.Str::slug($title).'.pdf',
            'file_format' => 'PDF',
            'position' => 0,
            'is_active' => true,
        ];
    }
}
