<?php

namespace App\Services\Employee;

use App\DTOs\EmployeeDTO;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly EmployeeRepository $employeeRepository
    ) {}

    /**
     * Get paginated list of employees.
     */   
    public function list(): LengthAwarePaginator
    {
        return $this->employeeRepository->paginate();
    }

    /**
     * Get a single employee by ID.
     */
    public function get(int $id): Employee
    {
        return $this->employeeRepository->find($id);
    }

    /**
     * Create a new employee.
     */
    public function create(EmployeeDTO $dto): Employee
    {
        return $this->employeeRepository->create([
            'name'   => $dto->name,
            'age'    => $dto->age,
            'job'    => $dto->job,
            'salary' => $dto->salary,
        ]);
    }

    /**
     * Update an existing employee.
     */
    public function update(Employee $employee, EmployeeDTO $dto): Employee
    {
        return $this->employeeRepository->update($employee, [
            'name'   => $dto->name,
            'age'    => $dto->age,
            'job'    => $dto->job,
            'salary' => $dto->salary,
        ]);
    }

    /**
     * Delete an employee.
     */
    public function delete(Employee $employee): void
    {
        $this->employeeRepository->delete($employee);
    }

}
