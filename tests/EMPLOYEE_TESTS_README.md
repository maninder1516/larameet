# Employee API Test Documentation

This document describes the test coverage for the Employee API endpoints and controller.

## Test Files

### 1. Unit Tests - `tests/Unit/EmployeeControllerTest.php`

Unit tests focus on testing the controller logic in isolation by mocking dependencies.

#### Test Cases:

1. **`it_can_list_employees()`**
   - Tests that the index method correctly returns a paginated list of employees
   - Verifies the service is called and response is formatted correctly

2. **`it_handles_exception_when_listing_employees()`**
   - Tests error handling when listing employees fails
   - Verifies appropriate error response and logging

3. **`it_can_store_an_employee()`**
   - Tests employee creation through the store method
   - Verifies DTO is created correctly and passed to service
   - Checks 201 status code is returned

4. **`it_handles_exception_when_storing_employee()`**
   - Tests error handling during employee creation
   - Verifies error logging and appropriate error response

5. **`it_can_show_an_employee()`**
   - Tests retrieving a single employee by ID
   - Verifies correct service method is called with correct ID

6. **`it_handles_exception_when_showing_employee()`**
   - Tests error handling when retrieving an employee fails
   - Verifies error response format

7. **`it_can_update_an_employee()`**
   - Tests employee update functionality
   - Verifies DTO is created with updated data
   - Checks service update method is called correctly

8. **`it_handles_exception_when_updating_employee()`**
   - Tests error handling during employee update
   - Verifies error logging and response

9. **`it_can_delete_an_employee()`**
   - Tests employee deletion
   - Verifies service delete method is called
   - Checks 204 No Content response

10. **`it_handles_exception_when_deleting_employee()`**
    - Tests error handling during employee deletion
    - Verifies error response format

### 2. Feature Tests - `tests/Feature/EmployeeApiTest.php`

Feature tests make actual HTTP requests to test the full application stack including routing, validation, database interactions, and responses.

#### Test Cases:

1. **`it_can_get_paginated_list_of_employees()`**
   - Tests GET /api/employees endpoint
   - Verifies pagination structure and employee data format

2. **`it_can_create_an_employee()`**
   - Tests POST /api/employees endpoint
   - Verifies employee is created in database
   - Checks response format and status code

3. **`it_validates_required_fields_when_creating_employee()`**
   - Tests validation for required fields (name, age, job, salary)
   - Verifies 422 status code for validation errors

4. **`it_validates_name_is_string_and_max_255_chars()`**
   - Tests name field validation
   - Verifies max length constraint

5. **`it_validates_age_is_integer_between_18_and_65()`**
   - Tests age validation boundaries
   - Checks minimum (18), maximum (65), and type validation

6. **`it_validates_job_is_string_and_max_255_chars()`**
   - Tests job field validation
   - Verifies max length constraint

7. **`it_validates_salary_is_numeric_and_min_zero()`**
   - Tests salary validation
   - Verifies non-negative constraint and numeric type

8. **`it_can_show_a_specific_employee()`**
   - Tests GET /api/employees/{id} endpoint
   - Verifies correct employee data is returned

9. **`it_returns_404_when_employee_not_found()`**
   - Tests 404 response for non-existent employee
   - Verifies error handling

10. **`it_can_update_an_employee()`**
    - Tests PUT /api/employees/{id} endpoint
    - Verifies database is updated correctly
    - Checks response data

11. **`it_validates_fields_when_updating_employee()`**
    - Tests validation during update
    - Verifies all required fields are validated

12. **`it_can_delete_an_employee()`**
    - Tests DELETE /api/employees/{id} endpoint
    - Verifies employee is removed from database
    - Checks 204 No Content response

13. **`it_returns_404_when_deleting_non_existent_employee()`**
    - Tests deletion of non-existent employee
    - Verifies 404 response

14. **`it_accepts_valid_age_boundaries()`**
    - Tests edge cases for age validation (18 and 65)
    - Verifies boundary values are accepted

15. **`it_accepts_zero_salary()`**
    - Tests that zero salary is valid
    - Verifies minimum boundary case

16. **`it_accepts_decimal_salary_values()`**
    - Tests that decimal salary values work correctly
    - Verifies float precision

17. **`it_can_handle_pagination_parameters()`**
    - Tests pagination with query parameters
    - Verifies pagination metadata

## Supporting Files

### Employee Factory - `database/factories/EmployeeFactory.php`

Provides test data generation for employees with:
- Random realistic names, ages, jobs, and salaries
- State modifiers for `junior()` and `senior()` employees
- Custom salary setter `withSalary()`

## Running the Tests

### Run all tests:
```bash
php artisan test
```

### Run only unit tests:
```bash
php artisan test --testsuite=Unit
```

### Run only feature tests:
```bash
php artisan test --testsuite=Feature
```

### Run specific test file:
```bash
php artisan test tests/Unit/EmployeeControllerTest.php
php artisan test tests/Feature/EmployeeApiTest.php
```

### Run specific test method:
```bash
php artisan test --filter it_can_create_an_employee
```

### Run with coverage (requires Xdebug):
```bash
php artisan test --coverage
```

## API Endpoints Tested

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/employees | List all employees (paginated) |
| POST | /api/employees | Create a new employee |
| GET | /api/employees/{id} | Get a specific employee |
| PUT | /api/employees/{id} | Update an employee |
| DELETE | /api/employees/{id} | Delete an employee |

## Validation Rules Tested

| Field | Rules |
|-------|-------|
| name | required, string, max:255 |
| age | required, integer, min:18, max:65 |
| job | required, string, max:255 |
| salary | required, numeric, min:0 |

## Test Coverage Summary

- ✅ All CRUD operations
- ✅ Input validation for all fields
- ✅ Edge cases and boundary values
- ✅ Error handling and exception cases
- ✅ HTTP status codes
- ✅ JSON response structure
- ✅ Database persistence
- ✅ Pagination functionality
- ✅ 404 error handling for missing resources

## Notes

- Tests use `RefreshDatabase` trait to reset database between tests
- Unit tests mock the `EmployeeService` to isolate controller logic
- Feature tests use the actual database (test database) for integration testing
- All tests follow Laravel naming conventions with descriptive method names
