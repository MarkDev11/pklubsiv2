<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalMahasiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nim' => fake()->unique()->numerify('########'),
            'nama' => fake()->name(),
            'kd_lokal' => fake()->optional()->lexify('???'),
            'jns_pkl' => fake()->randomElement(['Magang', 'MSIB']),
            'judul_pkl' => fake()->sentence(),
            'tempat_riset' => fake()->company(),
            'nama_mentor' => fake()->name(),
            'hp_mentor' => fake()->numerify('08##########'),
            'email_mentor' => fake()->unique()->companyEmail(),
            'email_perusahaan' => fake()->optional()->companyEmail(),
            'dosen_pa' => fake()->name(),
            'skm' => null,
            'proposal' => null,
            'lp' => null,
            'lpp' => null,
            'skp' => null,
            'nilai' => 0,
            'penilai' => null,
        ];
    }

    public function magang(): static
    {
        return $this->state(fn (array $attributes) => [
            'jns_pkl' => 'Magang',
        ]);
    }

    public function msib(): static
    {
        return $this->state(fn (array $attributes) => [
            'jns_pkl' => 'MSIB',
        ]);
    }

    public function withLaporan(): static
    {
        return $this->state(fn (array $attributes) => [
            'lp' => 'laporan_test.pdf',
            'lpp' => 'penilaian_test.pdf',
            'skp' => 'suratketerangan_test.pdf',
        ]);
    }

    public function dinilai(): static
    {
        return $this->state(fn (array $attributes) => [
            'lp' => 'laporan_test.pdf',
            'lpp' => 'penilaian_test.pdf',
            'skp' => 'suratketerangan_test.pdf',
            'nilai' => fake()->numberBetween(60, 100),
            'penilai' => fake()->name(),
        ]);
    }

    public function belumDinilai(): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai' => 0,
        ]);
    }
}
