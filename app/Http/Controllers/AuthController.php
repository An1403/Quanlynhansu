<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLogin()
    {
        if (Auth::check()) {
            if (request()->route()->getName() !== 'login') {
                return $this->redirectByRole(Auth::user()->role);
            }
        }
        
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'password.required' => 'Vui lòng nhập mật khẩu'
        ]);

        // Tìm user theo username
        $user = User::where('username', $request->username)->first();

        // Kiểm tra user có tồn tại không
        if (!$user) {
            return back()->with('error', 'Tên đăng nhập không tồn tại!');
        }

        // Kiểm tra trạng thái active
        if ($user->status == 0) {
            return back()->with('error', 'Tài khoản của bạn đã bị khóa!');
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Mật khẩu không chính xác!');
        }

        // Đăng nhập thành công
        Auth::login($user, $request->has('remember'));

        // Lưu thông tin vào session
        session([
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'role' => $user->role
        ]);

        // Ghi log hoạt động
        $this->logActivity('login', "Đăng nhập với tài khoản: {$user->username}");

        // Chuyển hướng theo role
        return $this->redirectByRole($user->role);
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $username = Auth::user()->username ?? 'Unknown';
        
        // Ghi log hoạt động
        $this->logActivity('logout', "Đăng xuất tài khoản: {$username}");

        Auth::logout();
        session()->flush();
        
        return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Chuyển hướng theo vai trò
     */
    private function redirectByRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Chào mừng Admin!');
            case 'accountant':
                return redirect()->route('accountant.dashboard')
                    ->with('success', 'Chào mừng Kế toán!');
            case 'employee':
                return redirect()->route('employee.dashboard')
                    ->with('success', 'Chào mừng Nhân viên!');
            default:
                return redirect()->route('login')
                    ->with('error', 'Vai trò không được xác định!');
        }
    }

    /**
     * Ghi log hoạt động
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