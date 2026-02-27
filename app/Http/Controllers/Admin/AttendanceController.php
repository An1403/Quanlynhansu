<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = DB::table('attendances')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->leftJoin('projects', 'attendances.project_id', '=', 'projects.id')
            ->select(
                'attendances.*',
                'employees.full_name',
                'employees.employee_code',
                'employees.photo',
                'projects.name as project_name'
            )
            ->orderBy('attendances.date', 'desc')
            ->paginate(15);

        return view('admin.attendances.index', compact('attendances'));
    }

    public function create()
    {
        $employees = DB::table('employees')
            ->where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        $projects = DB::table('projects')
            ->where('status', 'In progress')
            ->orderBy('name')
            ->get();

        return view('admin.attendances.create', compact('employees', 'projects'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'date' => 'required|date',
        'check_in' => 'nullable|date_format:H:i',
        'check_out' => 'nullable|date_format:H:i|after:check_in',
        'status' => 'required|in:Present,Leave,Absent',
        'project_id' => 'nullable|exists:projects,id',
        'notes' => 'nullable|string|max:255',
    ], [
        'employee_id.required' => 'Vui lòng chọn nhân viên',
        'employee_id.exists' => 'Nhân viên không tồn tại',
        'date.required' => 'Vui lòng chọn ngày',
        'check_in.date_format' => 'Giờ vào không đúng định dạng (HH:mm)',
        'check_out.date_format' => 'Giờ ra không đúng định dạng (HH:mm)',
        'check_out.after' => 'Giờ ra phải sau giờ vào',
    ]);

    try {
        DB::beginTransaction();

        $workingHours = 0;
        if ($request->check_in && $request->check_out) {
            $workingHours = $this->calculateWorkingHours(
                $request->check_in, 
                $request->check_out
            );
        }

        // Log chi tiết
        \Log::info('=== DEBUG ATTENDANCE ===', [
            'check_in_raw' => $request->check_in,
            'check_out_raw' => $request->check_out,
            'working_hours_calculated' => $workingHours,
            'check_in_formatted' => $request->check_in ? $request->check_in . ':00' : null,
            'check_out_formatted' => $request->check_out ? $request->check_out . ':00' : null,
        ]);


        $attendanceId = DB::table('attendances')->insertGetId([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'working_hours' => $workingHours, 
            'status' => $request->status,
            'project_id' => $request->project_id,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $inserted = DB::table('attendances')->where('id', $attendanceId)->first();
        \Log::info('=== AFTER INSERT ===', [
            'id' => $inserted->id,
            'working_hours_in_db' => $inserted->working_hours
        ]);
        $employee = DB::table('employees')->where('id', $request->employee_id)->first();
        $statusText = $this->getStatusText($request->status);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => "Thêm chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: {$request->date} - Trạng thái: {$statusText} - Giờ làm: {$workingHours}h",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        DB::commit();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Thêm chấm công thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Attendance create error: ' . $e->getMessage());
        
        return back()
            ->with('error', 'Lỗi thêm chấm công: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(string $id)
    {
        $attendance = DB::table('attendances')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->leftJoin('projects', 'attendances.project_id', '=', 'projects.id')
            ->where('attendances.id', $id)
            ->select(
                'attendances.*',
                'employees.full_name',
                'employees.employee_code',
                'employees.photo',
                'projects.name as project_name'
            )
            ->first();

        if (!$attendance) {
            return redirect()->route('admin.attendances.index')
                ->with('error', 'Không tìm thấy bản ghi chấm công!');
        }

        return view('admin.attendances.show', compact('attendance'));
    }

    public function edit(string $id)
    {
        $attendance = DB::table('attendances')
            ->where('id', $id)
            ->first();

        if (!$attendance) {
            return redirect()->route('admin.attendances.index')
                ->with('error', 'Không tìm thấy bản ghi chấm công!');
        }

        $employees = DB::table('employees')
            ->where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        $projects = DB::table('projects')
            ->orderBy('name')
            ->get();

        return view('admin.attendances.edit', compact('attendance', 'employees', 'projects'));
    }

    public function update(Request $request, string $id)
    {
        $attendance = DB::table('attendances')->where('id', $id)->first();

        if (!$attendance) {
            return redirect()->route('admin.attendances.index')
                ->with('error', 'Không tìm thấy bản ghi chấm công!');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            // KHÔNG validate working_hours
            'status' => 'required|in:Present,Leave,Absent',
            'project_id' => 'nullable|exists:projects,id',
            'notes' => 'nullable|string|max:255',
        ], [
            'employee_id.required' => 'Vui lòng chọn nhân viên',
            'date.required' => 'Vui lòng chọn ngày',
            'check_out.after' => 'Giờ ra phải sau giờ vào',
        ]);

        try {
            DB::beginTransaction();

            // ✅ BACKEND TỰ TÍNH
            $workingHours = 0;
            if ($request->check_in && $request->check_out) {
                $workingHours = $this->calculateWorkingHours(
                    $request->check_in, 
                    $request->check_out
                );
            }

            // Cập nhật chấm công
            DB::table('attendances')
                ->where('id', $id)
                ->update([
                    'employee_id' => $request->employee_id,
                    'date' => $request->date,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'working_hours' => $workingHours, // ← Dùng giá trị backend tính
                    'status' => $request->status,
                    'project_id' => $request->project_id,
                    'notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            // Lấy thông tin nhân viên
            $employee = DB::table('employees')->where('id', $request->employee_id)->first();
            $statusText = $this->getStatusText($request->status);

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Cập nhật chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: {$request->date} - Trạng thái: {$statusText} - Giờ làm: {$workingHours}h",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.attendances.index')
                ->with('success', 'Cập nhật chấm công thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attendance update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi cập nhật: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $attendance = DB::table('attendances')->where('id', $id)->first();

        if (!$attendance) {
            return redirect()->route('admin.attendances.index')
                ->with('error', 'Không tìm thấy bản ghi chấm công!');
        }

        try {
            DB::beginTransaction();

            // Lấy thông tin để ghi log
            $employee = DB::table('employees')->where('id', $attendance->employee_id)->first();
            $statusText = $this->getStatusText($attendance->status);

            // Xóa chấm công
            DB::table('attendances')->where('id', $id)->delete();

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Xóa chấm công: {$employee->full_name} ({$employee->employee_code}) - Ngày: {$attendance->date} - Trạng thái: {$statusText}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.attendances.index')
                ->with('success', 'Xóa bản ghi chấm công thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attendance delete error: ' . $e->getMessage());
            
            return redirect()->route('admin.attendances.index')
                ->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }

    /**
 * Tính số giờ làm từ check_in và check_out (TRỪ 1.5 TIẾNG NGHỈ TRƯA)
 */
private function calculateWorkingHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            \Log::warning('⚠️ Missing check_in or check_out');
            return 0;
        }

        try {
            // Parse từ H:i format (07:00, 16:30)
            $checkInTime = \Carbon\Carbon::createFromFormat('H:i', $checkIn);
            $checkOutTime = \Carbon\Carbon::createFromFormat('H:i', $checkOut);
            
            \Log::info('⏰ Parsed times:', [
                'check_in_parsed' => $checkInTime->format('H:i:s'),
                'check_out_parsed' => $checkOutTime->format('H:i:s')
            ]);
            
            // Kiểm tra check_out > check_in
            if ($checkOutTime->greaterThan($checkInTime)) {
                // Tính tổng số phút làm việc
                $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
                
                \Log::info('📊 Time calculation:', [
                    'total_minutes' => $totalMinutes,
                    'lunch_break' => 90,
                ]);
                
                // Trừ đi 1.5 tiếng nghỉ trưa (90 phút)
                $workingMinutes = $totalMinutes - 90;
                
                // Nếu âm thì trả về 0
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
                'trace' => $e->getTraceAsString()
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
}