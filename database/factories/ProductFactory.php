<?php
declare(strict_types=1);
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->text(6),
            'name' => $this->faker->unique()->name(),
            'price' => rand(1, 100),
            'suitableWeather' => (['clear', 'isolated-clouds', 'scattered-clouds', 'overcast', 'light-rain',
                'moderate-rain', 'heavy-rain', 'sleet', 'light-snow', 'moderate-snow', 'heavy-snow', 'fog', 'na']),
        ];
    }
}
