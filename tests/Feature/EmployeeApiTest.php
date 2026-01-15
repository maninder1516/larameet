<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Authenticate user for API testing
        Passport::actingAs(
            User::factory()->create()
        );
    }

    /** @test */
    public function it_can_get_paginated_list_of_employees()
    {
        // Arrange
        Employee::factory()->count(15)->create();

        // Act
        $response = $this->getJson('/api/employees');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'name', 'age', 'job', 'salary', 'created_at', 'updated_at']
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);
    }

    /** @test */
    public function it_can_create_an_employee()
    {
        // Arrange
        $employeeData = [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Software Engineer',
            'salary' => 75000.50
        ];

        // Act
        $response = $this->postJson('/api/employees', $employeeData);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'name', 'age', 'job', 'salary', 'created_at', 'updated_at'
        ]);
        $response->assertJson([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Software Engineer',
            'salary' => 75000.50
        ]);

        $this->assertDatabaseHas('employees', [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Software Engineer',
            'salary' => 75000.50
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_employee()
    {
        // Act
        $response = $this->postJson('/api/employees', []);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'age', 'job', 'salary']);
    }

    /** @test */
    public function it_validates_name_is_string_and_max_255_chars()
    {
        // Act - name too long
        $response = $this->postJson('/api/employees', [
            'name' => str_repeat('a', 256),
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_validates_age_is_integer_between_18_and_65()
    {
        // Test age too low
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 17,
            'job' => 'Developer',
            'salary' => 50000
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['age']);

        // Test age too high
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 66,
            'job' => 'Developer',
            'salary' => 50000
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['age']);

        // Test age not integer
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 'thirty',
            'job' => 'Developer',
            'salary' => 50000
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['age']);
    }

    /** @test */
    public function it_validates_job_is_string_and_max_255_chars()
    {
        // Act
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 30,
            'job' => str_repeat('a', 256),
            'salary' => 50000
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['job']);
    }

    /** @test */
    public function it_validates_salary_is_numeric_and_min_zero()
    {
        // Test negative salary
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => -1000
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['salary']);

        // Test non-numeric salary
        $response = $this->postJson('/api/employees', [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 'not-a-number'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['salary']);
    }

    /** @test */
    public function it_can_show_a_specific_employee()
    {
        // Arrange
        $employee = Employee::factory()->create([
            'name' => 'Jane Smith',
            'age' => 28,
            'job' => 'Designer',
            'salary' => 60000
        ]);

        // Act
        $response = $this->getJson("/api/employees/{$employee->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $employee->id,
            'name' => 'Jane Smith',
            'age' => 28,
            'job' => 'Designer',
            'salary' => 60000
        ]);
    }

    /** @test */
    public function it_returns_404_when_employee_not_found()
    {
        // Act
        $response = $this->getJson('/api/employees/999999');

        // Assert
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_an_employee()
    {
        // Arrange
        $employee = Employee::factory()->create([
            'name' => 'Original Name',
            'age' => 25,
            'job' => 'Junior Developer',
            'salary' => 40000
        ]);

        $updatedData = [
            'name' => 'Updated Name',
            'age' => 26,
            'job' => 'Mid-level Developer',
            'salary' => 55000.75
        ];

        // Act
        $response = $this->putJson("/api/employees/{$employee->id}", $updatedData);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $employee->id,
            'name' => 'Updated Name',
            'age' => 26,
            'job' => 'Mid-level Developer',
            'salary' => 55000.75
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Updated Name',
            'age' => 26,
            'job' => 'Mid-level Developer',
            'salary' => 55000.75
        ]);
    }

    /** @test */
    public function it_validates_fields_when_updating_employee()
    {
        // Arrange
        $employee = Employee::factory()->create();

        // Act
        $response = $this->putJson("/api/employees/{$employee->id}", []);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'age', 'job', 'salary']);
    }

    /** @test */
    public function it_can_delete_an_employee()
    {
        // Arrange
        $employee = Employee::factory()->create();

        // Act
        $response = $this->deleteJson("/api/employees/{$employee->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id
        ]);
    }

    /** @test */
    public function it_returns_404_when_deleting_non_existent_employee()
    {
        // Act
        $response = $this->deleteJson('/api/employees/999999');

        // Assert
        $response->assertStatus(404);
    }

    /** @test */
    public function it_accepts_valid_age_boundaries()
    {
        // Test age 18 (minimum valid)
        $response = $this->postJson('/api/employees', [
            'name' => 'Young Employee',
            'age' => 18,
            'job' => 'Intern',
            'salary' => 30000
        ]);
        $response->assertStatus(201);

        // Test age 65 (maximum valid)
        $response = $this->postJson('/api/employees', [
            'name' => 'Senior Employee',
            'age' => 65,
            'job' => 'Consultant',
            'salary' => 100000
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    public function it_accepts_zero_salary()
    {
        // Act
        $response = $this->postJson('/api/employees', [
            'name' => 'Volunteer',
            'age' => 25,
            'job' => 'Intern',
            'salary' => 0
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('employees', [
            'name' => 'Volunteer',
            'salary' => 0
        ]);
    }

    /** @test */
    public function it_accepts_decimal_salary_values()
    {
        // Act
        $response = $this->postJson('/api/employees', [
            'name' => 'Employee with Decimal Salary',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 75432.99
        ]);

        // Assert
        $response->assertStatus(201);
        $response->assertJson([
            'salary' => 75432.99
        ]);
    }

    /** @test */
    public function it_can_handle_pagination_parameters()
    {
        // Arrange
        Employee::factory()->count(30)->create();

        // Act
        $response = $this->getJson('/api/employees?page=2&per_page=10');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data',
            'per_page',
            'total'
        ]);
    }
}
