<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'positions';

    protected $fillable = [
        'name',
        'allowance'
    ];

    protected $casts = [
        'allowance' => 'decimal:2'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }
}