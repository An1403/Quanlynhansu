<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveRequestController extends Controller
{
    /**
     * Hiển thị danh sách đơn xin nghỉ
     */
    public function index()
    {
        $user = Auth::user();
        
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Thống kê
        $statistics = [
            'remaining' => 12,
            'approved' => LeaveRequest::where('user_id', $user->id)->approved()->count(),
            'pending' => LeaveRequest::where('user_id', $user->id)->pending()->count(),
            'rejected' => LeaveRequest::where('user_id', $user->id)->rejected()->count(),
        ];

        return view('employee.leave-requests.index', compact('leaveRequests', 'statistics'));
    }

    /**
     * Hiển thị form tạo đơn xin nghỉ
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();
        
        if ($leaveTypes->isEmpty()) {
            return back()->with('error', 'Chưa có loại nghỉ nào được cấu hình!');
        }

        return view('employee.leave-requests.create', compact('leaveTypes'));
    }

    /**
     * Lưu đơn xin nghỉ mới
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:500',
        ], [
            'leave_type_id.required' => 'Vui lòng chọn loại nghỉ phép',
            'leave_type_id.exists' => 'Loại nghỉ phép không hợp lệ',
            'from_date.required' => 'Vui lòng chọn ngày bắt đầu',
            'from_date.after_or_equal' => 'Ngày bắt đầu phải là ngày hôm nay hoặc sau đó',
            'to_date.required' => 'Vui lòng chọn ngày kết thúc',
            'to_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu',
            'reason.required' => 'Vui lòng nhập lý do nghỉ',
            'reason.max' => 'Lý do không được vượt quá 500 ký tự',
        ]);

        try {
            DB::beginTransaction();

            // Tạo đơn xin nghỉ
            $leaveRequest = LeaveRequest::create([
                'user_id' => $user->id,
                'types_id' => $validated['leave_type_id'],
                'start_date' => $validated['from_date'],
                'end_date' => $validated['to_date'],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            // Lấy tên loại nghỉ
            $leaveType = LeaveType::find($validated['leave_type_id']);
            $leaveTypeName = $leaveType ? $leaveType->name : 'N/A';

            // Tính số ngày nghỉ
            $fromDate = \Carbon\Carbon::parse($validated['from_date']);
            $toDate = \Carbon\Carbon::parse($validated['to_date']);
            $daysCount = $fromDate->diffInDays($toDate) + 1;

            // ✅ Ghi log
            $this->logActivity('create', 
                "Tạo đơn xin nghỉ: {$leaveTypeName} từ {$fromDate->format('d/m/Y')} đến {$toDate->format('d/m/Y')} ({$daysCount} ngày)"
            );

            DB::commit();

            return redirect()->route('employee.leave-requests.index')
                ->with('success', 'Tạo đơn xin nghỉ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave request create error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xem chi tiết đơn xin nghỉ
     */
    public function show($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::where('user_id', $user->id)
            ->with('leaveType')
            ->findOrFail($id);

        return view('employee.leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::where('user_id', $user->id)->findOrFail($id);
        
        if (!$leaveRequest->canEdit()) {
            return back()->with('error', 'Chỉ có thể chỉnh sửa đơn chờ duyệt!');
        }

        $leaveTypes = LeaveType::all();
        
        return view('employee.leave-requests.edit', compact('leaveRequest', 'leaveTypes'));
    }

    /**
     * Cập nhật đơn xin nghỉ
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::where('user_id', $user->id)->findOrFail($id);

        if (!$leaveRequest->canEdit()) {
            return back()->with('error', 'Chỉ có thể chỉnh sửa đơn chờ duyệt!');
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:500',
        ], [
            'leave_type_id.required' => 'Vui lòng chọn loại nghỉ phép',
            'to_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu',
            'reason.required' => 'Vui lòng nhập lý do nghỉ',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Lưu thông tin cũ để so sánh
            $oldData = [
                'types_id' => $leaveRequest->types_id,
                'start_date' => $leaveRequest->start_date,
                'end_date' => $leaveRequest->end_date,
                'reason' => $leaveRequest->reason,
            ];

            // Cập nhật
            $leaveRequest->update([
                'types_id' => $validated['leave_type_id'],
                'start_date' => $validated['from_date'],
                'end_date' => $validated['to_date'],
                'reason' => $validated['reason'],
            ]);

            // ✅ Ghi log chi tiết các thay đổi
            $changes = [];
            
            // So sánh loại nghỉ
            if ($oldData['types_id'] != $validated['leave_type_id']) {
                $oldType = LeaveType::find($oldData['types_id'])->name ?? 'N/A';
                $newType = LeaveType::find($validated['leave_type_id'])->name ?? 'N/A';
                $changes[] = "Loại nghỉ: {$oldType} → {$newType}";
            }
            
            // So sánh ngày bắt đầu
            if ($oldData['start_date'] != $validated['from_date']) {
                $oldDate = \Carbon\Carbon::parse($oldData['start_date'])->format('d/m/Y');
                $newDate = \Carbon\Carbon::parse($validated['from_date'])->format('d/m/Y');
                $changes[] = "Từ ngày: {$oldDate} → {$newDate}";
            }
            
            // So sánh ngày kết thúc
            if ($oldData['end_date'] != $validated['to_date']) {
                $oldDate = \Carbon\Carbon::parse($oldData['end_date'])->format('d/m/Y');
                $newDate = \Carbon\Carbon::parse($validated['to_date'])->format('d/m/Y');
                $changes[] = "Đến ngày: {$oldDate} → {$newDate}";
            }
            
            // So sánh lý do (chỉ log nếu thay đổi đáng kể)
            if ($oldData['reason'] != $validated['reason']) {
                $changes[] = "Đã cập nhật lý do nghỉ";
            }

            $leaveType = LeaveType::find($validated['leave_type_id']);
            $description = "Cập nhật đơn xin nghỉ: {$leaveType->name}";
            if (!empty($changes)) {
                $description .= " (" . implode(', ', $changes) . ")";
            }

            $this->logActivity('update', $description);

            DB::commit();

            return redirect()->route('employee.leave-requests.index')
                ->with('success', 'Cập nhật đơn xin nghỉ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave request update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa đơn xin nghỉ
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::where('user_id', $user->id)->findOrFail($id);

        if (!$leaveRequest->canDelete()) {
            return back()->with('error', 'Chỉ có thể hủy đơn chờ duyệt!');
        }

        try {
            DB::beginTransaction();

            // ✅ Lưu thông tin trước khi xóa
            $leaveType = $leaveRequest->leaveType;
            $leaveTypeName = $leaveType ? $leaveType->name : 'N/A';
            $startDate = \Carbon\Carbon::parse($leaveRequest->start_date)->format('d/m/Y');
            $endDate = \Carbon\Carbon::parse($leaveRequest->end_date)->format('d/m/Y');

            // Xóa đơn
            $leaveRequest->delete();

            // ✅ Ghi log
            $this->logActivity('delete', 
                "Hủy đơn xin nghỉ: {$leaveTypeName} từ {$startDate} đến {$endDate}"
            );

            DB::commit();

            return redirect()->route('employee.leave-requests.index')
                ->with('success', 'Hủy đơn xin nghỉ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave request delete error: ' . $e->getMessage());
            
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // ===== HELPER METHODS =====

    /**
     * ✅ Ghi log activity - Giống ProjectController
     */
    private function logActivity($action, $description)
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}