<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        // ========== THỐNG KÊ CƠ BẢN ==========
        $stats = [
            // Lương tháng này
            'total_salary_this_month' => DB::table('salaries')
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->sum('total_salary'),
            
            'employees_with_salary' => DB::table('salaries')
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->distinct('employee_id')
                ->count('employee_id'),
            
            'salary_records_this_month' => DB::table('salaries')
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->count(),
            
            // Giờ làm việc
            'total_working_hours' => DB::table('salaries')
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->sum('total_hours'),
            
            'avg_working_hours' => DB::table('salaries')
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->avg('total_hours') ?? 0,
            
            // Nhân viên chưa có lương
            'employees_without_salary' => DB::table('employees')
                ->where('status', 'Active')
                ->whereNotExists(function($query) use ($currentMonth, $currentYear) {
                    $query->select(DB::raw(1))
                        ->from('salaries')
                        ->whereRaw('salaries.employee_id = employees.id')
                        ->where('month', $currentMonth)
                        ->where('year', $currentYear);
                })
                ->count(),
        ];

        // ========== TÓM TẮT TÀI CHÍNH ==========
        $financial_summary = DB::table('salaries')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->selectRaw('
                SUM(base_salary + allowance + bonus) as total_income,
                SUM(deduction) as total_deduction,
                SUM(total_salary) as net_salary,
                AVG(total_salary) as avg_salary
            ')
            ->first();

        $financial_summary = [
            'total_income' => $financial_summary->total_income ?? 0,
            'total_deduction' => $financial_summary->total_deduction ?? 0,
            'net_salary' => $financial_summary->net_salary ?? 0,
            'avg_salary' => $financial_summary->avg_salary ?? 0,
        ];

        // ========== BẢNG LƯƠNG MỚI NHẤT ==========
        $recent_salaries = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select(
                'salaries.*',
                'employees.full_name',
                'employees.employee_code'
            )
            ->orderBy('salaries.created_at', 'desc')
            ->limit(10)
            ->get();

        // ========== CHẤM CÔNG HÔM NAY ==========
        $today_attendance = DB::table('attendances')
            ->whereDate('date', today())
            ->get();

        $attendance_stats = [
            'present' => $today_attendance->where('status', 'Present')->count(),
            'leave' => $today_attendance->where('status', 'Leave')->count(),
            'absent' => $today_attendance->where('status', 'Absent')->count(),
        ];

        // ========== BIỂU ĐỒ LƯƠNG 6 THÁNG ==========
        $salary_chart = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $total = DB::table('salaries')
                ->where('month', $month)
                ->where('year', $year)
                ->sum('total_salary');
            
            $salary_chart->push((object)[
                'month' => $month,
                'year' => $year,
                'total_salary' => $total,
            ]);
        }

        // ========== LƯƠNG THEO PHÒNG BAN (THÁNG NÀY) ==========
        $salary_by_department = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->where('salaries.month', $currentMonth)
            ->where('salaries.year', $currentYear)
            ->select(
                'departments.name as department_name',
                DB::raw('COUNT(DISTINCT salaries.employee_id) as employee_count'),
                DB::raw('SUM(salaries.base_salary) as total_base_salary'),
                DB::raw('SUM(salaries.allowance) as total_allowance'),
                DB::raw('SUM(salaries.bonus) as total_bonus'),
                DB::raw('SUM(salaries.deduction) as total_deduction'),
                DB::raw('SUM(salaries.total_salary) as total_salary')
            )
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('total_salary', 'desc')
            ->get();

        return view('accountant.dashboard', compact(
            'stats',
            'financial_summary',
            'recent_salaries',
            'today_attendance',
            'attendance_stats',
            'salary_chart',
            'salary_by_department'
        ));
    }
}