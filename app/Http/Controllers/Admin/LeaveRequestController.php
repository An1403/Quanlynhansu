<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    /**
     * Hiển thị danh sách đơn xin phép
     */
    public function index()
    {
        $leaveRequests = DB::table('leave_requests')
            ->join('users', 'leave_requests.user_id', '=', 'users.id')
            ->join('employees', 'users.id', '=', 'employees.user_id')  // Join thêm bảng employees
            ->leftJoin('leave_types', 'leave_requests.types_id', '=', 'leave_types.id')  // Join thêm leave_types
            ->select(
                'leave_requests.*',
                'users.username',
                'employees.full_name',  // Lấy full_name từ bảng employees
                'employees.employee_code',
                'leave_types.name as leave_type_name'  // Tên loại nghỉ phép
            )
            ->orderBy('leave_requests.created_at', 'desc')
            ->paginate(15);

        return view('admin.leave-requests.index', compact('leaveRequests'));
    }

    /**
     * Hiển thị chi tiết đơn xin phép
     */
    public function show(string $id)
    {
        $leaveRequest = DB::table('leave_requests')
            ->join('users', 'leave_requests.user_id', '=', 'users.id')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->leftJoin('leave_types', 'leave_requests.types_id', '=', 'leave_types.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->select(
                'leave_requests.*',
                'users.username',
                'employees.full_name',
                'employees.employee_code',
                'employees.phone',
                'employees.email',
                'departments.name as department_name',
                'positions.name as position_name',
                'leave_types.name as leave_type_name',
                'leave_types.days_available'
            )
            ->where('leave_requests.id', $id)
            ->first();

        if (!$leaveRequest) {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'Không tìm thấy đơn xin phép!');
        }

        return view('admin.leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Phê duyệt đơn xin phép
     */
    public function approve(string $id)
    {
        $leaveRequest = DB::table('leave_requests')->where('id', $id)->first();

        if (!$leaveRequest) {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'Không tìm thấy đơn xin phép!');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.show', $id)
                ->with('error', 'Đơn xin phép này đã được xử lý!');
        }

        DB::table('leave_requests')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'approve',
            'description' => 'Phê duyệt đơn xin phép ID: ' . $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.leave-requests.show', $id)
            ->with('success', 'Đã phê duyệt đơn xin phép thành công!');
    }

    /**
     * Từ chối đơn xin phép
     */
    public function reject(string $id)
    {
        $leaveRequest = DB::table('leave_requests')->where('id', $id)->first();

        if (!$leaveRequest) {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'Không tìm thấy đơn xin phép!');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.show', $id)
                ->with('error', 'Đơn xin phép này đã được xử lý!');
        }

        DB::table('leave_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => now()
            ]);

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'reject',
            'description' => 'Từ chối đơn xin phép ID: ' . $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.leave-requests.show', $id)
            ->with('success', 'Đã từ chối đơn xin phép!');
    }
}