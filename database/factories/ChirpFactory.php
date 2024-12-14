<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\chirp;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\chirp>
 */
class ChirpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Chirp::class;
    public function definition()
    {
        return [
            'message' => $this->faker->text(255), // Le contenu du Chirp
            'user_id' => User::factory(), // Assurez-vous d'avoir une relation avec le modèle User
        ];
    }
}
