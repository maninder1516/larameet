<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeRepository
{
    /** 
     * Get paginated list of employees.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::paginate($perPage);
    }

    /**
     * Get a single employee by ID.
     */
    public function find(int $id): Employee
    {
        return Employee::findOrFail($id);
    }

    /**
     * Create a new employee.
     */
    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    /**
     * Update an existing employee.
     */
    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }
    /**
     * Delete an employee.
     */
    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
