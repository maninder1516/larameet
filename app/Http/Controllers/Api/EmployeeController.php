<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\DTOs\EmployeeDTO;
use App\Services\Employee\EmployeeService;


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
        return response()->json(
            $this->employeeService->list()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string',
            'age'    => 'required|integer|min:18|max:65',
            'job'    => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

        $employee = $this->employeeService->create(
            new EmployeeDTO(
                $validated['name'],
                $validated['age'],
                $validated['job'],
                (float) $validated['salary']
            )
        );

        return response()->json($employee, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json(
            $this->employeeService->get($employee->id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'   => 'required|string',
            'age'    => 'required|integer|min:18|max:65',
            'job'    => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->employeeService->delete($employee);

        return response()->json(null, 204);
    }
}
