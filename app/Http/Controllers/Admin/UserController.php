<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản người dùng
     */
    public function index()
    {
        $users = User::with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo tài khoản mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu tài khoản mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,employee,accountant',
            'status' => 'required|in:0,1',
        ], [
            'username.required' => 'Tên đăng nhập không được để trống',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'full_name.required' => 'Họ và tên không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu không trùng khớp',
            'role.required' => 'Vai trò không được để trống',
        ]);

        try {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => (int)$validated['status'],
            ]);

            $this->logActivity('create', "Tạo tài khoản mới: {$user->username}");

            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error('User create error: ' . $e->getMessage());
            return back()
                ->with('error', 'Lỗi tạo tài khoản: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết tài khoản
     */
    public function show(User $user)
    {
        $user->load('employee');
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa tài khoản
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật tài khoản
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'full_name' => 'required|string|max:100',
            'role' => 'required|in:admin,employee,accountant',
            'status' => 'required|in:0,1',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'username.required' => 'Tên đăng nhập không được để trống',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'full_name.required' => 'Họ và tên không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu không trùng khớp',
            'role.required' => 'Vai trò không được để trống',
        ]);

        try {
            // Xử lý mật khẩu
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['status'] = (int)$validated['status'];

            $user->update($validated);

            $this->logActivity('update', "Cập nhật tài khoản: {$user->username}");

            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error('User update error: ' . $e->getMessage());
            return back()
                ->with('error', 'Lỗi cập nhật: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa tài khoản
     */
    public function destroy(User $user)
{
    // Không cho phép xóa tài khoản hiện tại
    if ($user->id === Auth::id()) {
        return back()->with('error', 'Không thể xóa tài khoản đang sử dụng!');
    }

    try {
        $username = $user->username;

        DB::beginTransaction();
        
        // Xóa employee trước (nếu có)
        if ($user->employee) {
            // Kiểm tra xem employee có đang quản lý dự án không
            if ($user->employee->managed_projects()->count() > 0) {
                return back()->with('error', 'Không thể xóa! Nhân viên này đang quản lý dự án.');
            }
            
            // Xóa các liên kết project_employees
            $user->employee->projects()->detach();
            
            // Xóa employee
            $user->employee->delete();
        }
        
        // Xóa user
        $user->delete();

        DB::commit();

        $this->logActivity('delete', "Xóa tài khoản: {$username}");

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa tài khoản thành công!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('User delete error: ' . $e->getMessage());
        return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
    }
}

    /**
     * Reset mật khẩu
     */
    public function resetPassword(User $user)
    {
        try {
            $newPassword = Str::random(10);

            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            $this->logActivity('reset_password', "Reset mật khẩu cho: {$user->username}");

            return back()
                ->with('success', "Mật khẩu mới: $newPassword. Vui lòng gửi cho người dùng qua email.");
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Khóa/Mở khóa tài khoản
     */
    public function toggleStatus(User $user)
    {
        try {
            $newStatus = $user->status == 1 ? 0 : 1;
            $user->update(['status' => $newStatus]);

            $statusText = $newStatus == 1 ? 'mở khóa' : 'khóa';
            
            $this->logActivity('toggle_status', "Tài khoản {$user->username} đã được {$statusText}");

            return back()->with('success', "Tài khoản đã được {$statusText}!");
        } catch (\Exception $e) {
            Log::error('Toggle status error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang quản lý phân quyền
     */
    public function roles()
    {
        $users = User::with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.users.roles', compact('users'));
    }

    /**
     * Cập nhật phân quyền
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,employee,accountant',
        ], [
            'role.required' => 'Vai trò không được để trống',
        ]);

        try {
            $oldRole = $user->role;
            $user->update(['role' => $validated['role']]);

            $this->logActivity(
                'update_role', 
                "Cập nhật vai trò {$user->username} từ {$oldRole} sang {$validated['role']}"
            );

            return back()->with('success', 'Cập nhật phân quyền thành công!');
        } catch (\Exception $e) {
            Log::error('Update role error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị nhật ký hoạt động
     */
    public function activity()
    {
        $activities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.users.activity', compact('activities'));
    }

    /**
     * Helper: Ghi nhật ký hoạt động
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
            Log::error('Error logging activity: ' . $e->getMessage());
        }
    }
}