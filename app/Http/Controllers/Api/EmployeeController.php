<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\DTOs\EmployeeDTO;
use App\Services\Employee\EmployeeService;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Log;
use Exception;


class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(
                $this->employeeService->list()
            );
        } catch (Exception $e) {
            Log::error('Failed to list employees', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to retrieve employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $employee = $this->employeeService->create(
                new EmployeeDTO(
                    $validated['name'],
                    $validated['age'],
                    $validated['job'],
                    (float) $validated['salary']
                )
            );

            return response()->json($employee, 201);
        } catch (Exception $e) {
            Log::error('Failed to create employee', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        try {
            return response()->json(
                $this->employeeService->get($employee->id)
            );
        } catch (Exception $e) {
            Log::error('Failed to show employee', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to retrieve employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $validated = $request->validated();

            $employee = $this->employeeService->update(
                $employee,
                new EmployeeDTO(
                    $validated['name'],
                    $validated['age'],
                    $validated['job'],
                    (float) $validated['salary']
                )
            );

            return response()->json($employee);
        } catch (Exception $e) {
            Log::error('Failed to update employee', [
                'employee_id' => $employee->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->delete($employee);

            return response()->json(null, 204);
        } catch (Exception $e) {
            Log::error('Failed to delete employee', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
