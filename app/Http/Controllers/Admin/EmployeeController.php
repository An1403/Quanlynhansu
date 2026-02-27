<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class EmployeeController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên
     */
    public function index()
    {
        $employees = Employee::with(['user', 'department', 'position'])->paginate(15);
        
        return view('admin.employees.index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Hiển thị form tạo nhân viên
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('admin.employees.create', [
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }

    /**
     * Lưu nhân viên mới và tạo tài khoản
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'identity_card' => 'nullable|string|max:20|unique:employees,identity_card',
            'identity_card_issued_at' => 'nullable|string|max:100',
            'identity_card_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:Nam,Nữ',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'join_date' => 'nullable|date',
            'base_salary' => 'nullable|numeric|min:0',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $username = $request->username;
            $password = $request->password;

            // 3. Tạo tài khoản User
            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'full_name' => $request->full_name,
                'email' => $request->email,
                'identity_card' => $request->identity_card,
                'role' => 'employee',
                'status' => 1,
            ]);

            // 4. Generate mã nhân viên
            $employeeCode = $this->generateEmployeeCode();

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $photoPath = $file->storeAs('employees/photos', $filename, 'public');
            }
            // 5. Tạo record Employee và liên kết với User qua user_id
            $employee = Employee::create([
                'user_id' => $user->id, // Liên kết với User
                'employee_code' => $employeeCode,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'gender' => $request->gender ?? 'Male',
                'identity_card' => $request->identity_card,
                'identity_card_issued_at' => $request->identity_card_issued_at,
                'identity_card_date' => $request->identity_card_date,
                'photo' => $photoPath,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'join_date' => $request->join_date,
                'base_salary' => $request->base_salary ?? 0,
                'status' => 'Active',
            ]);

            DB::commit();
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Tạo nhân viên mới: {$employee->full_name} ({$employeeCode})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            // 6. Hiển thị thông tin tài khoản cho admin
            return redirect()->route('admin.employees.index')
                ->with('success', "Tạo nhân viên thành công!\n\n" .
                    "Thông tin đăng nhập:\n" .
                    "Username: $username\n" .
                    "Mật khẩu: $password\n" .
                    "Email: {$user->email}");

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return back()
                ->with('error', 'Lỗi tạo nhân viên: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết nhân viên
     */
    public function show(Employee $employee)
{
    // Load relationships
    $employee->load(['user', 'department', 'position', 'attendances']);

    // Tính toán thống kê
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $stats = [
        // Chấm công tháng này
        'attendance_this_month' => DB::table('attendances')
            ->where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Present')
            ->count(),
        
        // Tổng giờ làm tháng này
        'total_hours_this_month' => DB::table('attendances')
            ->where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('working_hours') ?? 0,
        
        // Đơn xin nghỉ tháng này 
        'leave_requests_this_month' => DB::table('leave_requests')
            ->where('user_id', $employee->user_id)  
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count(),
        
        // Lương tháng gần nhất
        'latest_salary' => DB::table('salaries')
            ->where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->value('total_salary'),
    ];

    return view('admin.employees.show', compact('employee', 'stats'));
}

    /**
     * Hiển thị form chỉnh sửa nhân viên
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();

        $employee->load(['user', 'department', 'position']);

        return view('admin.employees.edit', [
            'employee' => $employee,
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }

    /**
     * Cập nhật nhân viên
     */
    public function update(Request $request, Employee $employee)
    {
        
        $request->validate([
        'full_name' => 'required|string|max:100',
        'email' => 'required|email|unique:employees,email,' . $employee->id, // ← SỬA ĐÂY
        'identity_card' => 'nullable|string|max:20|unique:employees,identity_card,' . $employee->id,
        'identity_card_issued_at' => 'nullable|string|max:100',
        'identity_card_date' => 'nullable|date',
        'phone' => 'nullable|string|max:15',
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string',
        'gender' => 'nullable|in:Nam,Nữ',
        'department_id' => 'nullable|exists:departments,id',
        'position_id' => 'nullable|exists:positions,id',
        'join_date' => 'nullable|date',
        'base_salary' => 'nullable|numeric|min:0',
        'status' => 'required|in:Active,Resigned',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'full_name.required' => 'Họ và tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'identity_card.unique' => 'Số CMND/CCCD đã tồn tại',
            'photo.image' => 'File phải là hình ảnh',
            'photo.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'photo.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ]);

        try {
            DB::beginTransaction();
            $photoPath = $employee->photo;
            if ($request->hasFile('photo')) {
                // Xóa ảnh cũ
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                
                // Upload ảnh mới
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $photoPath = $file->storeAs('employees/photos', $filename, 'public');
            }
           

            // Cập nhật thông tin Employee
            $employee->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'identity_card' => $request->identity_card,
                'identity_card_issued_at' => $request->identity_card_issued_at,
                'identity_card_date' => $request->identity_card_date,
                'photo' => $photoPath,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'join_date' => $request->join_date,
                'base_salary' => $request->base_salary,
                'status' => $request->status,
            ]);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Cập nhật nhân viên: {$employee->full_name} ({$employee->employee_code})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            DB::commit();

            return redirect()->route('admin.employees.show', $employee)
                ->with('success', 'Cập nhật nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($photoPath) && $photoPath != $employee->photo && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return back()
                ->with('error', 'Lỗi cập nhật: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa nhân viên 
     */
    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            $userId = $employee->user_id;

            // Xóa Employee
            $employee->delete();

            // Xóa User nếu tồn tại
            if ($userId) {
                User::find($userId)->delete();
            }
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Xoá nhân viên: {$employee->full_name} ({$employee->employee_code})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Xóa nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Reset mật khẩu nhân viên
     */
    public function resetPassword(Employee $employee)
    {
        try {
            if (!$employee->user) {
                return back()->with('error', 'Nhân viên này không có tài khoản!');
            }

            $newPassword = Str::random(10);

            $employee->user->update([
                'password' => Hash::make($newPassword)
            ]);

            return back()
                ->with('success', "Mật khẩu mới: $newPassword");

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // /**
    //  * Generate username từ full_name (ví dụ: Nguyễn Văn A -> nguyen.van.a)
    //  */
    // private function generateUsername($fullName)
    // {
    //     // Chuyển sang chữ thường
    //     $username = strtolower(trim($fullName));
        
    //     // Thay khoảng trắng bằng dấu chấm
    //     $username = str_replace(' ', '.', $username);
        
    //     // Xóa ký tự đặc biệt, chỉ giữ a-z, 0-9 và dấu chấm
    //     $username = preg_replace('/[^a-z0-9.]/', '', $username);

    //     // Kiểm tra xem username đã tồn tại chưa
    //     $baseUsername = $username;
    //     $counter = 1;

    //     while (User::where('username', $username)->exists()) {
    //         $username = $baseUsername . $counter;
    //         $counter++;
    //     }

    //     return $username;
    // }

    /**
     * Generate employee code (EMP-0001, EMP-0002, ...)
     */
    private function generateEmployeeCode()
    {
        $lastEmployee = Employee::orderBy('id', 'desc')->first();
        
        if (!$lastEmployee || !$lastEmployee->employee_code) {
            return 'EMP-0001';
        }

        // Extract số từ code cũ (ví dụ: EMP-0001 -> 0001)
        preg_match('/(\d+)$/', $lastEmployee->employee_code, $matches);
        $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;

        return 'EMP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}