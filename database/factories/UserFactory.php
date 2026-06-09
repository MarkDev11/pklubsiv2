<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => strtoupper(fake()->unique()->lexify('????????')),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Mahasiswa->value,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin->value,
        ]);
    }

    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Dosen->value,
        ]);
    }

    public function mentor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Mentor->value,
        ]);
    }

    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Mahasiswa->value,
            'jenis' => 'Magang',
            'nama_dosen_pa' => 'DSN000001',
            'kd_lokal' => strtoupper(fake()->lexify('???')),
        ]);
    }
}
