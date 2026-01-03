<?php

namespace App\DTOs;

class EmployeeDTO
{
    public function __construct(
        public string $name,
        public int $age,
        public string $job,
        public float $salary
    ) {}
}
