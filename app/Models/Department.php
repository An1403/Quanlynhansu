<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function activeEmployees()
    {
        return $this->hasMany(Employee::class, 'department_id')->where('status', 'Active');
    }

    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }
}