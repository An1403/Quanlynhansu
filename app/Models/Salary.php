<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'total_hours',
        'base_salary',
        'allowance',
        'bonus',
        'deduction',
        'total_salary',
    ];

    protected $casts = [
        'total_hours' => 'float',
        'base_salary' => 'float',
        'allowance' => 'float',
        'bonus' => 'float',
        'deduction' => 'float',
        'total_salary' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Scopes
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('year', 'desc')->orderBy('month', 'desc');
    }

    /**
     * Accessors
     */
    public function getFormattedMonthAttribute()
    {
        return sprintf('%02d/%04d', $this->month, $this->year);
    }

    /**
     * Methods
     */
    public function calculateTotalSalary()
    {
        $this->total_salary = ($this->base_salary + $this->allowance + $this->bonus) - $this->deduction;
        return $this->total_salary;
    }

    public static function generateSalary($employeeId, $month, $year)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return null;
        }

        $totalHours = Attendance::byEmployee($employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('working_hours');

        $allowance = $employee->position ? $employee->position->allowance : 0;

        $salary = new self();
        $salary->employee_id = $employeeId;
        $salary->month = $month;
        $salary->year = $year;
        $salary->total_hours = $totalHours;
        $salary->base_salary = $employee->base_salary;
        $salary->allowance = $allowance;
        $salary->bonus = 0;
        $salary->deduction = 0;
        $salary->total_salary = $salary->calculateTotalSalary();

        return $salary;
    }
}