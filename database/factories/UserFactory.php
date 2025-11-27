<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
 protected $model = User::class;

public function definition(): array
{
    $role = $this->faker->randomElement(['admin', 'student']);
    $campuses = ['Echague', 'Angadanan', 'Jones', 'Ilagan'];

    return [
        'name' => $this->faker->name(),
        'campus' => $this->faker->randomElement($campuses), // choose from predefined
        'email' => $role === 'admin' ? $this->faker->unique()->safeEmail() : null,
        'student_id' => $role === 'student' ? $this->faker->unique()->numerify('S########') : null,
        'lrn' => $role === 'student' ? $this->faker->unique()->numerify('LRN########') : null,
        'role' => $role,
        'password' => bcrypt('password'), // default password
        'remember_token' => \Illuminate\Support\Str::random(10),
    ];
}


    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'email' => $this->faker->unique()->safeEmail(),
            'student_id' => null,
            'lrn' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
            'email' => null,
            'student_id' => $this->faker->unique()->numerify('S########'),
            'lrn' => $this->faker->unique()->numerify('LRN########'),
        ]);
    }
}
