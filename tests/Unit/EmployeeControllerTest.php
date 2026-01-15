<?php

namespace Tests\Unit;

use App\DTOs\EmployeeDTO;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\Employee\EmployeeService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    private EmployeeService $employeeService;
    private EmployeeController $controller;

    /**
     * Setup the test environment before each test method.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp(); // !! MUST call parent::setUp() first !!
        
        // Our custom setup code goes here
        $this->employeeService = Mockery::mock(EmployeeService::class);
        $this->controller = new EmployeeController($this->employeeService);
    }

    /**
     * Clean up the test environment after each test method.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown(); // !! MUST call parent::tearDown() last !!
    }

    /** @test */
    public function it_can_list_employees()
    {
        // Arrange
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->employeeService
            ->shouldReceive('list')
            ->once()
            ->andReturn($paginator);

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_exception_when_listing_employees()
    {
        // Arrange
        Log::shouldReceive('error')->once();
        $this->employeeService
            ->shouldReceive('list')
            ->once()
            ->andThrow(new Exception('Database error'));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to retrieve employees', $data['message']);
        $this->assertEquals('Database error', $data['error']);
    }

    /** @test */
    public function it_can_store_an_employee()
    {
        // Arrange
        $requestData = [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ];
        
        $request = Mockery::mock(StoreEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);
        $request->shouldReceive('all')->andReturn($requestData);

        $employee = new Employee($requestData);
        $employee->id = 1;

        $this->employeeService
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($dto) use ($requestData) {
                return $dto instanceof EmployeeDTO &&
                       $dto->name === $requestData['name'] &&
                       $dto->age === $requestData['age'] &&
                       $dto->job === $requestData['job'] &&
                       $dto->salary === $requestData['salary'];
            }))
            ->andReturn($employee);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_exception_when_storing_employee()
    {
        // Arrange
        $requestData = [
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ];
        
        $request = Mockery::mock(StoreEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);
        $request->shouldReceive('all')->andReturn($requestData);

        Log::shouldReceive('error')->once();
        $this->employeeService
            ->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Failed to save'));

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to create employee', $data['message']);
    }

    /** @test */
    public function it_can_show_an_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        $this->employeeService
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn($employee);

        // Act
        $response = $this->controller->show($employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_exception_when_showing_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        Log::shouldReceive('error')->once();
        $this->employeeService
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andThrow(new Exception('Not found'));

        // Act
        $response = $this->controller->show($employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to retrieve employee', $data['message']);
    }

    /** @test */
    public function it_can_update_an_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        $updatedData = [
            'name' => 'Jane Doe',
            'age' => 32,
            'job' => 'Senior Developer',
            'salary' => 60000.00
        ];

        $request = Mockery::mock(UpdateEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($updatedData);
        $request->shouldReceive('all')->andReturn($updatedData);

        $updatedEmployee = new Employee($updatedData);
        $updatedEmployee->id = 1;

        $this->employeeService
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(fn($e) => $e->id === 1),
                Mockery::on(function ($dto) use ($updatedData) {
                    return $dto instanceof EmployeeDTO &&
                           $dto->name === $updatedData['name'] &&
                           $dto->age === $updatedData['age'] &&
                           $dto->job === $updatedData['job'] &&
                           $dto->salary === $updatedData['salary'];
                })
            )
            ->andReturn($updatedEmployee);

        // Act
        $response = $this->controller->update($request, $employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_exception_when_updating_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        $updatedData = [
            'name' => 'Jane Doe',
            'age' => 32,
            'job' => 'Senior Developer',
            'salary' => 60000.00
        ];

        $request = Mockery::mock(UpdateEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($updatedData);
        $request->shouldReceive('all')->andReturn($updatedData);

        Log::shouldReceive('error')->once();
        $this->employeeService
            ->shouldReceive('update')
            ->once()
            ->andThrow(new Exception('Update failed'));

        // Act
        $response = $this->controller->update($request, $employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to update employee', $data['message']);
    }

    /** @test */
    public function it_can_delete_an_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        $this->employeeService
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn($e) => $e->id === 1));

        // Act
        $response = $this->controller->destroy($employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_exception_when_deleting_employee()
    {
        // Arrange
        $employee = new Employee([
            'name' => 'John Doe',
            'age' => 30,
            'job' => 'Developer',
            'salary' => 50000.00
        ]);
        $employee->id = 1;

        Log::shouldReceive('error')->once();
        $this->employeeService
            ->shouldReceive('delete')
            ->once()
            ->andThrow(new Exception('Delete failed'));

        // Act
        $response = $this->controller->destroy($employee);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to delete employee', $data['message']);
    }
}
