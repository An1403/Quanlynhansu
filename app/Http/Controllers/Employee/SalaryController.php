<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class SalaryController extends Controller
{
    /**
     * Display a listing of the employee's salaries.
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin nhân viên.');
        }

        // Lấy danh sách lương của nhân viên, sắp xếp theo năm và tháng giảm dần
        $salaries = Salary::where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Lương tháng hiện tại
        $currentMonth = date('n');
        $currentYear = date('Y');
        $currentMonthSalary = Salary::where('employee_id', $employee->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        // Tổng thu nhập
        $totalIncome = Salary::where('employee_id', $employee->id)
            ->sum('total_salary');

        return view('employee.salary-slip.index', compact(
            'salaries',
            'currentMonthSalary',
            'totalIncome'
        ));
    }

    /**
     * Display the specified salary.
     */
    public function show($id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Chỉ cho phép xem lương của chính mình
        $salary = Salary::where('id', $id)
            ->where('employee_id', $employee->id)
            ->with(['employee.department', 'employee.position'])
            ->firstOrFail();

        // Tính toán chi tiết
        $workingDaysInMonth = 26; // Số ngày làm việc tiêu chuẩn
        $dailySalary = $salary->base_salary / $workingDaysInMonth;
        $hourlyRate = $dailySalary / 8;

        return view('employee.salary-slip.show', compact(
            'salary',
            'workingDaysInMonth',
            'dailySalary',
            'hourlyRate'
        ));
    }

    /**
     * Print salary slip.
     */
    public function print($id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Chỉ cho phép in lương của chính mình
        $salary = Salary::where('id', $id)
            ->where('employee_id', $employee->id)
            ->with(['employee.department', 'employee.position'])
            ->firstOrFail();

        $workingDaysInMonth = 26;
        $dailySalary = $salary->base_salary / $workingDaysInMonth;
        $hourlyRate = $dailySalary / 8;

        $pdf = Pdf::loadView('employee.salary-slip.print', compact(
            'salary',
            'workingDaysInMonth',
            'dailySalary',
            'hourlyRate'
        ));

        return $pdf->stream('phieu-luong-' . $salary->month . '-' . $salary->year . '.pdf');
    }
}