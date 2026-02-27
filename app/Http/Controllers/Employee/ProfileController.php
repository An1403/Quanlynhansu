<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Hiển thị hồ sơ cá nhân
     */
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Lấy thống kê tháng này
        $statistics = $this->getMonthlyStatistics($employee->id);

        return view('employee.profile.index', compact('user', 'employee', 'statistics'));
    }

    /**
     * Hiển thị form chỉnh sửa hồ sơ cá nhân
     */
    public function edit()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        return view('employee.profile.edit', compact('user', 'employee'));
    }

    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Nam,Nữ',
            'identity_card' => 'nullable|string|max:20|unique:employees,identity_card,' . $employee->id,
            'identity_card_issued_at' => 'nullable|string|max:255',
            'identity_card_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'full_name.required' => 'Họ và tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'phone.max' => 'Số điện thoại không quá 15 ký tự',
            'identity_card.unique' => 'Số CMND/CCCD đã tồn tại',
            'photo.image' => 'File phải là hình ảnh',
            'photo.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg',
            'photo.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Lưu thông tin cũ để so sánh
            $oldData = [
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'gender' => $employee->gender,
                'identity_card' => $employee->identity_card,
            ];

            // Chuẩn bị dữ liệu cập nhật
            $updateData = [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'identity_card' => $validated['identity_card'] ?? null,
                'identity_card_issued_at' => $validated['identity_card_issued_at'] ?? null,
                'identity_card_date' => $validated['identity_card_date'] ?? null,
            ];

            $photoUpdated = false;
            
            // Xử lý upload ảnh
            if ($request->hasFile('photo')) {
                // Xóa ảnh cũ nếu có
                if ($employee->photo && file_exists(storage_path('app/public/' . $employee->photo))) {
                    unlink(storage_path('app/public/' . $employee->photo));
                }

                // Lưu ảnh mới
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('employees/photos', $filename, 'public');
                $updateData['photo'] = $path;
                $photoUpdated = true;
            }

            // Cập nhật employee
            $employee->update($updateData);

            // ✅ Ghi log chi tiết các thay đổi (giống ProjectController)
            $changes = [];
            
            if ($oldData['full_name'] !== $validated['full_name']) {
                $changes[] = "Họ tên: {$oldData['full_name']} → {$validated['full_name']}";
            }
            
            if ($oldData['email'] !== $validated['email']) {
                $changes[] = "Email: {$oldData['email']} → {$validated['email']}";
            }
            
            if ($oldData['phone'] !== ($validated['phone'] ?? null)) {
                $changes[] = "SĐT: " . ($oldData['phone'] ?? 'Chưa có') . " → " . ($validated['phone'] ?? 'Chưa có');
            }
            
            if ($oldData['gender'] !== ($validated['gender'] ?? null)) {
                $changes[] = "Giới tính: " . ($oldData['gender'] ?? 'Chưa có') . " → " . ($validated['gender'] ?? 'Chưa có');
            }
            
            if ($oldData['identity_card'] !== ($validated['identity_card'] ?? null)) {
                $changes[] = "CMND/CCCD: " . ($oldData['identity_card'] ?? 'Chưa có') . " → " . ($validated['identity_card'] ?? 'Chưa có');
            }
            
            if ($photoUpdated) {
                $changes[] = "Cập nhật ảnh đại diện mới";
            }

            $description = "Cập nhật hồ sơ cá nhân: {$employee->full_name} ({$employee->employee_code})";
            if (!empty($changes)) {
                $description .= " (" . implode(', ', $changes) . ")";
            }

            // ✅ Ghi log activity
            $this->logActivity('update', $description);

            DB::commit();

            return redirect()->route('employee.profile.index')
                ->with('success', 'Cập nhật hồ sơ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload ảnh đại diện
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->firstOrFail();

            // Xóa ảnh cũ
            if ($employee->photo && file_exists(storage_path('app/public/' . $employee->photo))) {
                unlink(storage_path('app/public/' . $employee->photo));
            }

            // Lưu ảnh mới
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('employees/photos', $filename, 'public');

            $employee->update(['photo' => $path]);

            // ✅ Ghi log
            $this->logActivity('update', 
                "Cập nhật ảnh đại diện: {$employee->full_name} ({$employee->employee_code}) - File: {$filename}"
            );

            DB::commit();

            return back()->with('success', 'Cập nhật ảnh đại diện thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Avatar upload error: ' . $e->getMessage());
            
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

    /**
     * Lấy thống kê tháng này
     */
    private function getMonthlyStatistics($employeeId)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $workedDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $leaveDays = $attendances->where('status', 'Leave')->count();
        
        // Tính số lần đi muộn (check_in > 08:00)
        $lateTimes = $attendances->filter(function ($attendance) {
            if (!$attendance->check_in) {
                return false;
            }
            
            try {
                $checkInTime = $attendance->check_in instanceof Carbon 
                    ? $attendance->check_in 
                    : Carbon::parse($attendance->check_in);
                return $checkInTime->format('H:i') > '08:00';
            } catch (\Exception $e) {
                return false;
            }
        })->count();

        $totalHours = $attendances->sum('working_hours') ?? 0;

        return [
            'worked_days' => $workedDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'late_times' => $lateTimes,
            'total_hours' => round($totalHours, 1),
        ];
    }
}