<?php

namespace Database\Factories;

use App\Models\Advertising;
use App\Models\Channel;
use App\Models\MainChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Advertising::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'main_channel_id' => MainChannel::all()->random(),
            'channel_name' => $this->faker->name,
            'name' => $this->faker->name,
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
        ];
    }
}
