<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // The required trait
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory; // Using the trait
    // Enables the factory() method: The trait adds a static factory() method to 
    // our Eloquent model, which you can use to create instances of the 
    // model's associated factory.
    //Employee::factory()->create(); // Creates one record
    //Employee::factory()->count(10)->create(); // Creates ten records

    protected $fillable = [
        'name', 'age', 'job', 'salary'
    ];
}
