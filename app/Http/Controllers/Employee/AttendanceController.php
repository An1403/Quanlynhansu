<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Hiển thị danh sách chấm công
     */
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $query = Attendance::where('employee_id', $employee->id);

        $month = request('month');
        $year = request('year', date('Y'));

        if ($month) {
            $query->whereMonth('date', $month);
        }

        if ($year) {
            $query->whereYear('date', $year);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);
        $statistics = $this->getStatistics($employee->id, $month, $year);

        return view('employee.attendance.index', compact('attendances', 'statistics', 'month', 'year'));
    }

    /**
     * Hiển thị form tạo chấm công
     */
    public function create()
    {
        $projects = Project::where('status', '!=', 'Completed')->get();
        return view('employee.attendance.create', compact('projects'));
    }

    /**
     * Lưu chấm công mới
     */
    public function store(Request $request)
    {
        try {
            // Lấy employee hiện tại
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();
            
            if (!$employee) {
                return back()
                    ->with('error', 'Không tìm thấy thông tin nhân viên!')
                    ->withInput();
            }

            // Validate
            $request->validate([
                'date' => 'required|date',
                'check_in' => 'nullable|date_format:H:i',
                'check_out' => 'nullable|date_format:H:i|after:check_in',
                'status' => 'required|in:Present,Leave,Absent',
                'project_id' => 'nullable|exists:projects,id',
                'notes' => 'nullable|string|max:255',
            ], [
                'check_out.after' => 'Giờ ra phải sau giờ vào',
                'date.required' => 'Vui lòng chọn ngày',
                'status.required' => 'Vui lòng chọn trạng thái',
            ]);

            // Kiểm tra trùng lặp
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $request->date)
                ->first();

            if ($existingAttendance) {
                return back()
                    ->with('error', 'Đã có chấm công cho ngày này!')
                    ->withInput();
            }

            DB::beginTransaction();

            // ✅ Tính giờ làm TRỪ 1.5 TIẾNG NGHỈ TRƯA
            $workingHours = 0;
            if ($request->check_in && $request->check_out) {
                $workingHours = $this->calculateWorkingHours(
                    $request->check_in,
                    $request->check_out
                );
            }

            // Log chi tiết
            \Log::info('=== EMPLOYEE ATTENDANCE CREATE ===', [
                'employee_id' => $employee->id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'working_hours_calculated' => $workingHours,
            ]);

            // Chuyển đổi time format (H:i → H:i:s)
            $checkIn = $request->check_in ? $request->check_in . ':00' : null;
            $checkOut = $request->check_out ? $request->check_out . ':00' : null;

            // Tạo attendance record
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'date' => $request->date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'working_hours' => $workingHours, // ← Giá trị đã trừ 1.5h nghỉ trưa
                'status' => $request->status,
                'project_id' => $request->project_id ?? null,
                'notes' => $request->notes ?? null,
            ]);
            \Log::info('=== AFTER CREATE ===', [
                'id' => $attendance->id,
                'working_hours_before' => $workingHours,
                'working_hours_after' => $attendance->working_hours,
                'working_hours_from_db' => $attendance->getAttributes()['working_hours'], // Giá trị thô từ DB
            ]);

            // Kiểm tra lại trong database
            $fromDb = Attendance::find($attendance->id);
            \Log::info('=== RECHECK FROM DATABASE ===', [
                'id' => $fromDb->id,
                'check_in' => $fromDb->check_in,
                'check_out' => $fromDb->check_out,
                'working_hours' => $fromDb->working_hours,
                'working_hours_raw' => $fromDb->getAttributes()['working_hours'],
                ]);
            // ✅ Ghi Activity Log
            $statusText = $this->getStatusText($request->status);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Nhân viên tạo chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: {$request->date} - Trạng thái: {$statusText} - Giờ làm: {$workingHours}h",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('employee.attendance.index')
                ->with('success', 'Thêm chấm công thành công!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee attendance store error: ' . $e->getMessage());
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết chấm công
     */
    public function show($id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::with('project')
            ->where('employee_id', $employee->id)
            ->findOrFail($id);

        return view('employee.attendance.show', compact('attendance'));
    }

    /**
     * Hiển thị form chỉnh sửa chấm công
     */
    public function edit($id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->findOrFail($id);

        $projects = Project::where('status', '!=', 'Completed')->get();

        return view('employee.attendance.edit', compact('attendance', 'projects'));
    }

    /**
     * Cập nhật chấm công
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:Present,Leave,Absent',
            'project_id' => 'nullable|exists:projects,id',
            'notes' => 'nullable|string|max:255',
        ], [
            'check_out.after' => 'Giờ ra phải sau giờ vào',
            'date.required' => 'Vui lòng chọn ngày',
        ]);

        try {
            DB::beginTransaction();

            // Lưu giá trị cũ để so sánh
            $oldWorkingHours = $attendance->working_hours;
            $oldDate = $attendance->date;

            // ✅ Tính giờ làm TRỪ 1.5 TIẾNG NGHỈ TRƯA
            $workingHours = 0;
            if ($request->check_in && $request->check_out) {
                $workingHours = $this->calculateWorkingHours(
                    $request->check_in,
                    $request->check_out
                );
            }

            // Chuyển đổi time format
            $checkIn = $request->check_in ? $request->check_in . ':00' : null;
            $checkOut = $request->check_out ? $request->check_out . ':00' : null;

            $attendance->update([
                'date' => $request->date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'working_hours' => $workingHours, // ← Giá trị đã trừ 1.5h nghỉ trưa
                'status' => $request->status,
                'project_id' => $request->project_id ?? null,
                'notes' => $request->notes ?? null,
            ]);

            // ✅ Ghi Activity Log - Chi tiết những gì thay đổi
            $changes = [];
            
            if ($oldDate != $request->date) {
                $changes[] = "Ngày: " . date('d/m/Y', strtotime($oldDate)) . " → " . date('d/m/Y', strtotime($request->date));
            }
            
            if ($oldWorkingHours != $workingHours) {
                $changes[] = "Giờ làm: {$oldWorkingHours}h → {$workingHours}h";
            }

            $changeDescription = !empty($changes) ? ' (' . implode(', ', $changes) . ')' : '';
            $statusText = $this->getStatusText($request->status);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Nhân viên cập nhật chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: {$request->date} - Trạng thái: {$statusText}" . $changeDescription,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('employee.attendance.index')
                ->with('success', 'Cập nhật chấm công thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee attendance update error: ' . $e->getMessage());
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa chấm công
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->findOrFail($id);

        try {
            DB::beginTransaction();

            // Lưu thông tin để ghi log
            $date = $attendance->date;
            $statusText = $this->getStatusText($attendance->status);
            $workingHours = $attendance->working_hours;

            // Xóa chấm công
            $attendance->delete();

            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Nhân viên xóa chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: " . date('d/m/Y', strtotime($date)) . " - Trạng thái: {$statusText} - Giờ làm: {$workingHours}h",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('employee.attendance.index')
                ->with('success', 'Xóa chấm công thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee attendance delete error: ' . $e->getMessage());
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Check in (API)
     */
    public function checkIn(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->firstOrFail();

            $today = date('Y-m-d');
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if ($attendance && $attendance->check_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã check-in hôm nay rồi!',
                ], 400);
            }

            $checkInTime = now();

            if ($attendance) {
                $attendance->update([
                    'check_in' => $checkInTime,
                    'status' => 'Present',
                ]);
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'check_in' => $checkInTime,
                    'status' => 'Present',
                ]);
            }

            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Check-in: {$employee->full_name} ({$employee->employee_code}) - " . $checkInTime->format('H:i:s'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công!',
                'check_in' => $checkInTime->format('H:i:s'),
                'date' => $today,
            ]);

        } catch (\Exception $e) {
            Log::error('Check-in error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check out (API)
     */
    public function checkOut(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->firstOrFail();

            $today = date('Y-m-d');
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->check_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa check-in hôm nay!',
                ], 400);
            }

            if ($attendance->check_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã check-out rồi!',
                ], 400);
            }

            $checkOutTime = now();
            
            // ✅ Tính giờ làm TRỪ 1.5 TIẾNG
            $checkInStr = Carbon::parse($attendance->check_in)->format('H:i');
            $checkOutStr = $checkOutTime->format('H:i');
            $workingHours = $this->calculateWorkingHours($checkInStr, $checkOutStr);

            $attendance->update([
                'check_out' => $checkOutTime,
                'working_hours' => $workingHours,
            ]);

            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Check-out: {$employee->full_name} ({$employee->employee_code}) - " . $checkOutTime->format('H:i:s') . " - Giờ làm: {$workingHours}h",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out thành công!',
                'check_out' => $checkOutTime->format('H:i:s'),
                'working_hours' => $workingHours,
            ]);

        } catch (\Exception $e) {
            Log::error('Check-out error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✅ TÍNH GIỜ LÀM TRỪ 1.5 TIẾNG NGHỈ TRƯA (giống Admin)
     */
    private function calculateWorkingHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            \Log::warning('⚠️ Missing check_in or check_out');
            return 0;
        }

        try {
            // Parse từ H:i format
            $checkInTime = Carbon::createFromFormat('H:i', $checkIn);
            $checkOutTime = Carbon::createFromFormat('H:i', $checkOut);
            
            \Log::info('⏰ Parsed times:', [
                'check_in_parsed' => $checkInTime->format('H:i:s'),
                'check_out_parsed' => $checkOutTime->format('H:i:s')
            ]);
            
            if ($checkOutTime->greaterThan($checkInTime)) {
                // Tính tổng số phút
                $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
                
                \Log::info('📊 Time calculation:', [
                    'total_minutes' => $totalMinutes,
                    'lunch_break' => 90,
                ]);
                
                // TRỪ 90 PHÚT NGHỈ TRƯA (1.5 tiếng)
                $workingMinutes = $totalMinutes - 90;
                
                if ($workingMinutes <= 0) {
                    \Log::warning('⚠️ Working minutes is negative or zero', [
                        'working_minutes' => $workingMinutes
                    ]);
                    return 0;
                }
                
                // Chuyển sang giờ (làm tròn 2 chữ số)
                $hours = round($workingMinutes / 60, 2);
                
                \Log::info('✅ Final result:', [
                    'working_minutes' => $workingMinutes,
                    'working_hours' => $hours
                ]);
                
                return $hours;
            } else {
                \Log::warning('⚠️ Check out time is not greater than check in time');
                return 0;
            }
        } catch (\Exception $e) {
            \Log::error('❌ Calculate working hours error:', [
                'message' => $e->getMessage(),
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);
        }
        
        return 0;
    }

    /**
     * Lấy text tiếng Việt cho status
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'Present' => 'Có mặt',
            'Leave' => 'Nghỉ phép',
            'Absent' => 'Vắng mặt',
        ];

        return $statusMap[$status] ?? $status;
    }

    /**
     * Lấy thống kê chấm công
     */
    private function getStatistics($employeeId, $month = null, $year = null)
    {
        $query = Attendance::where('employee_id', $employeeId);

        if ($month) {
            $query->whereMonth('date', $month);
        }

        if ($year) {
            $query->whereYear('date', $year);
        } else {
            $query->whereYear('date', date('Y'));
        }

        $attendances = $query->get();

        return [
            'worked_days' => $attendances->where('status', 'Present')->count(),
            'leave_days' => $attendances->where('status', 'Leave')->count(),
            'absent_days' => $attendances->where('status', 'Absent')->count(),
            'late_times' => $attendances->filter(function ($attendance) {
                if (!$attendance->check_in) {
                    return false;
                }
                
                try {
                    if ($attendance->check_in instanceof Carbon) {
                        return $attendance->check_in->format('H:i') > '08:00';
                    }
                    
                    $checkInTime = Carbon::parse($attendance->check_in);
                    return $checkInTime->format('H:i') > '08:00';
                } catch (\Exception $e) {
                    return false;
                }
            })->count(),
            'total_hours' => round($attendances->sum('working_hours'), 2),
        ];
    }
}