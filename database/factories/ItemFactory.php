<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Data palsu untuk barang (dipakai di testing).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('BRG-###'),
            'name' => fake()->words(3, true),
            'category_id' => Category::factory(),
            'unit' => fake()->randomElement(Item::UNITS),
            'min_stock' => 5,
        ];
    }
}
