<?php

namespace Biigle\Modules\AuthNfdi\Database\Factories;

use Biigle\Modules\AuthNfdi\NfdiLoginId;
use Biigle\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfdiLoginIdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NfdiLoginId::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
        ];
    }
}
