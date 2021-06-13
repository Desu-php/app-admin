<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'avatar' => $this->faker->text(255),
            'name' => $this->faker->name,
            'origin_name' => $this->faker->firstName,
            'url' => $this->faker->domainName
        ];
    }
}
