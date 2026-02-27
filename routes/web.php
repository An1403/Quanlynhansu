<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboard;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendance;
use App\Http\Controllers\Employee\LeaveRequestController as EmployeeLeaveRequest;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfile;
use App\Http\Controllers\Employee\ProjectController as EmployeeProject;
use App\Http\Controllers\Employee\SalaryController as EmployeeSalary;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboard;
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/*
|--------------------------------------------------------------------------
| Admin Routes (Yêu cầu đăng nhập và role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Quản lý nhân viên
    Route::resource('employees', EmployeeController::class);
    
    // Quản lý phòng ban
    Route::resource('departments', DepartmentController::class);
    
    // Quản lý chức vụ
    Route::resource('positions', PositionController::class);
    
    // Quản lý dự án
    Route::resource('projects', ProjectController::class);
    
    // Quản lý chấm công
    Route::resource('attendances', AttendanceController::class);
    
    // Quản lý lương 
    Route::get('/salaries/export', [SalaryController::class, 'export'])->name('salaries.export');
    Route::resource('salaries', SalaryController::class);
   
    // Quản lý đơn xin nghỉ
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
    });
    
    // Quản lý người dùng
    Route::resource('users', UserController::class);
    Route::get('/users-roles', [UserController::class, 'roles'])->name('users.roles');
    Route::post('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::get('/users-activity', [UserController::class, 'activity'])->name('users.activity');
});

/*
|--------------------------------------------------------------------------
| Employee Routes (Yêu cầu đăng nhập và role employee)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    
    // Dashboard
    Route::get('dashboard', [EmployeeDashboard::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('profile', [EmployeeProfile::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [EmployeeProfile::class, 'edit'])->name('profile.edit');
    Route::put('profile', [EmployeeProfile::class, 'update'])->name('profile.update');
    Route::put('profile/show', [EmployeeProfile::class, 'show'])->name('profile.show');
    Route::post('profile/upload-avatar', [EmployeeProfile::class, 'uploadAvatar'])->name('profile.upload-avatar');
    
    Route::resource('attendance', EmployeeAttendance::class);
    Route::post('attendance/check-in', [EmployeeAttendance::class, 'checkIn'])->name('attendance.check-in');
    Route::post('attendance/check-out', [EmployeeAttendance::class, 'checkOut'])->name('attendance.check-out');
    
    // Leave Requests
    Route::resource('leave-requests', EmployeeLeaveRequest::class);
    Route::post('leave-requests', [EmployeeLeaveRequest::class, 'store'])->name('leave-requests.store');
    
    // Projects
    Route::get('projects', [EmployeeProject::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [EmployeeProject::class, 'show'])->name('projects.show');
    
    // Salary Slip
    Route::get('salary-slip', [EmployeeSalary::class, 'index'])->name('salary-slip.index');
    Route::get('salary-slip/{salary}', [EmployeeSalary::class, 'show'])->name('salary-slip.show');
});


// Accountant Routes
Route::middleware(['auth', 'role:accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AccountantDashboard::class, 'index'])->name('dashboard');
    
    // Salaries Management
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
    
});
/*
|--------------------------------------------------------------------------
| Logout & Settings Routes
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    // Change Password
    Route::post('/change-password', [
        \App\Http\Controllers\Auth\ChangePasswordController::class, 'update'
    ])->name('change-password');

    // Settings
    Route::put('/settings/{section}', [
        \App\Http\Controllers\Auth\SettingsController::class, 'update'
    ])->name('settings.update');
});