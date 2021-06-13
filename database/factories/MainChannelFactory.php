<?php

namespace Database\Factories;

use App\Models\MainChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class MainChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MainChannel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'user_url' => $this->faker->url,
            'avatar' => null,
            'status' => 'public'
        ];
    }
}
