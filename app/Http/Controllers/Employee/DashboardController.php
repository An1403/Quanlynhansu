<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Project;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard của nhân viên
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $employee = Employee::where('user_id', $user->id)
        ->with('department', 'position')  // Thêm dòng này
        ->first();

        
        if (!$employee) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Lỗi tạo thông tin nhân viên. Vui lòng liên hệ admin!');
        }

        // Lấy thống kê
        $stats = $this->getStats($employee->id, $user->id);

        // Lấy các đơn xin nghỉ gần đây (5 bản ghi cuối cùng)
        $recent_leaves = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy dự án được giao cho nhân viên (bỏ whereHas vì bảng project_employees chưa có dữ liệu)
        $assigned_projects = Project::where('status', '!=', 'Completed')
            ->orderBy('end_date', 'asc')
            ->limit(5)
            ->get();

        // Lấy thống kê chấm công 7 ngày gần nhất
        $attendance_chart = Attendance::where('employee_id', $employee->id)
            ->select(
                'date',
                DB::raw('IF(status = "Present", 1, 0) as present')
            )
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->orderBy('date', 'asc')
            ->get();

        return view('employee.dashboard', [
            'employee' => $employee,
            'stats' => $stats,
            'recent_leaves' => $recent_leaves,
            'assigned_projects' => $assigned_projects,
            'attendance_chart' => $attendance_chart,
        ]);
    }

    /**
     * Lấy hoặc tạo employee
     */
    private function getOrCreateEmployee($user)
    {
        try {
            // Kiểm tra employee đã tồn tại chưa
            $employee = Employee::where('user_id', $user->id)->first();
            
            // Nếu đã tồn tại, trả về
            if ($employee) {
                return $employee;
            }

            // Tạo employee mới
            $employeeCode = 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
            
            // Kiểm tra xem employee_code đã tồn tại chưa (tránh trùng)
            $counter = 1;
            $originalCode = $employeeCode;
            
            while (Employee::where('employee_code', $employeeCode)->exists()) {
                $employeeCode = $originalCode . '-' . $counter;
                $counter++;
            }

            // Tạo employee mới
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $employeeCode,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'status' => 'Active'
            ]);

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy thống kê cho dashboard
     */
    private function getStats($employeeId, $userId)
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        try {
            // Đếm ngày đi làm trong tháng hiện tại
            $attendance_count = Attendance::where('employee_id', $employeeId)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Present')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error counting attendance: ' . $e->getMessage());
            $attendance_count = 0;
        }

        try {
            // Đếm ngày nghỉ phép được duyệt trong tháng hiện tại
            $leave_count = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereDate('start_date', '>=', $now->copy()->startOfMonth())
                ->whereDate('end_date', '<=', $now->copy()->endOfMonth())
                ->count();
        } catch (\Exception $e) {
            Log::error('Error counting leaves: ' . $e->getMessage());
            $leave_count = 0;
        }

        try {
            // Đếm số dự án không bị hoàn thành
            $assigned_projects = Project::where('status', '!=', 'Completed')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error counting projects: ' . $e->getMessage());
            $assigned_projects = 0;
        }

        // Công việc chưa hoàn (placeholder)
        $pending_tasks = 0;

        return [
            'attendance_count' => $attendance_count,
            'leave_count' => $leave_count,
            'assigned_projects' => $assigned_projects,
            'pending_tasks' => $pending_tasks,
        ];
    }
}