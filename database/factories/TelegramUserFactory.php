<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\MainChannel;
use App\Models\TelegramUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TelegramUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ['вошел', 'вышел'];
        return [
            //
            'user_id' => $this->faker->word,
            'main_channel_id' => MainChannel::all()->random(),
            'status' => $status[rand(0,1)],
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'avatar' => $this->faker->firstName,
            'advertisings' => $this->faker->name,
            'username' => $this->faker->userName,
            'bot' => $this->faker->word,
            'user_status' => $this->faker->word,
            'scam' => $this->faker->word,
            'resricted' => $this->faker->word,
            'restriction_reason' => $this->faker->word,

        ];
    }
}
