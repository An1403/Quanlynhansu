<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== THỐNG KÊ CƠ BẢN ==========
        $stats = [
            // Nhân viên
            'total_employees' => DB::table('employees')
                ->where('status', 'Active')
                ->count(),
            
            'active_employees' => DB::table('employees')
                ->where('status', 'Active')
                ->count(),
            
            'resigned_employees' => DB::table('employees')
                ->where('status', 'Resigned')
                ->count(),
            
            // Phòng ban & vị trí
            'total_departments' => DB::table('departments')->count(),
            
            'total_projects' => DB::table('projects')->count(),
            
            'active_projects' => DB::table('projects')
                ->where('status', 'In progress')
                ->count(),
            
            'completed_projects' => DB::table('projects')
                ->where('status', 'Completed')
                ->count(),
            
            // Chấm công
            'today_attendance' => DB::table('attendances')
                ->whereDate('date', today())
                ->where('status', 'Present')
                ->count(),
            
            // Đơn xin nghỉ
            'pending_leaves' => DB::table('leave_requests')
                ->where('status', 'pending')
                ->count(),
        ];

        // ========== DANH SÁCH NHÂN VIÊN MỚI NHẤT ==========
        $recent_employees = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->select(
                'employees.*',
                'departments.name as department_name',
                'positions.name as position_name'
            )
            ->orderBy('employees.created_at', 'desc')
            ->limit(5)
            ->get();

        // ========== DANH SÁCH ĐƠN XIN NGHỈ CHỜ DUYỆT ==========
        $pending_leave_requests = DB::table('leave_requests')
            ->join('users', 'leave_requests.user_id', '=', 'users.id')
            ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
            ->leftJoin('leave_types', 'leave_requests.types_id', '=', 'leave_types.id')
            ->select(
                'leave_requests.*',
                'employees.full_name',
                'employees.employee_code',
                'leave_types.name as leave_type_name'
            )
            ->where('leave_requests.status', 'pending')
            ->orderBy('leave_requests.created_at', 'desc')
            ->limit(5)
            ->get();

        // ========== THỐNG KÊ CHẤM CÔNG (7 NGÀY) - ĐÃ SỬA ==========
        // Tạo mảng 7 ngày gần nhất
        $attendance_chart = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Đếm số người chấm công Present trong ngày đó
            $count = DB::table('attendances')
                ->whereDate('date', $date)
                ->where('status', 'Present')
                ->count();
            
            $attendance_chart->push((object)[
                'date' => $date->format('Y-m-d'),
                'total' => $count,
                'day_name' => $date->locale('vi')->isoFormat('dddd'), // Thứ 2, Thứ 3...
            ]);
        }

        // ========== DỰ ÁN SẮP HẾT HẠN ==========
        $upcoming_projects = DB::table('projects')
            ->where('status', 'In progress')
            ->whereNotNull('end_date')
            ->where('end_date', '>=', today())
            ->where('end_date', '<=', today()->addDays(30))
            ->orderBy('end_date', 'asc')
            ->limit(5)
            ->get();

        // ========== NHÂN VIÊN THEO PHÒNG BAN ==========
        $employees_by_department = DB::table('departments')
            ->leftJoin('employees', function($join) {
                $join->on('departments.id', '=', 'employees.department_id')
                     ->where('employees.status', '=', 'Active');
            })
            ->select(
                'departments.id',
                'departments.name as department_name',
                DB::raw('COUNT(employees.id) as employee_count')
            )
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('employee_count', 'desc')
            ->get();

        // ========== HOẠT ĐỘNG GẦN ĐÂY ==========
        $recent_activities = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select(
                'activity_logs.*',
                'users.username'
            )
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_employees',
            'pending_leave_requests',
            'attendance_chart',
            'upcoming_projects',
            'employees_by_department',
            'recent_activities'
        ));
    }
}