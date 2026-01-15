<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 65),
            'job' => fake()->randomElement([
                'Software Engineer',
                'Senior Developer',
                'Project Manager',
                'Designer',
                'Data Analyst',
                'DevOps Engineer',
                'QA Engineer',
                'Product Manager',
                'Business Analyst',
                'Technical Lead'
            ]),
            'salary' => fake()->randomFloat(2, 30000, 150000),
        ];
    }

    /**
     * Indicate that the employee is a junior.
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'age' => fake()->numberBetween(18, 25),
            'job' => 'Junior Developer',
            'salary' => fake()->randomFloat(2, 30000, 50000),
        ]);
    }

    /**
     * Indicate that the employee is a senior.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'age' => fake()->numberBetween(35, 65),
            'job' => 'Senior ' . fake()->randomElement(['Developer', 'Engineer', 'Architect']),
            'salary' => fake()->randomFloat(2, 80000, 150000),
        ]);
    }

    /**
     * Indicate that the employee has a specific salary.
     */
    public function withSalary(float $salary): static
    {
        return $this->state(fn (array $attributes) => [
            'salary' => $salary,
        ]);
    }
}
