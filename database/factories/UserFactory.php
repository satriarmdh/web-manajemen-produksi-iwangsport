<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;
    
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),


            'role' => 'admin',
            'online_status' => false,
            'last_seen' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => ['role' => 'admin']);
    }

    public function owner(): static
    {
        return $this->state(fn(array $attributes) => ['role' => 'owner']);
    }

    public function potong(): static
    {
        return $this->state(fn(array $attributes) => ['role' => 'potong']);
    }

    public function jahit(): static
    {
        return $this->state(fn(array $attributes) => ['role' => 'jahit']);
    }

    public function finishing(): static
    {
        return $this->state(fn(array $attributes) => ['role' => 'finishing']);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
