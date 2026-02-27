<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'settings' => 'array',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Lấy thông tin employee của user
     * Relationship: User hasOne Employee
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Lấy danh sách activity logs của user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * Lấy danh sách đơn xin nghỉ của user
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    /**
     * Kiểm tra user có phải admin không
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra user có phải employee không
     */
    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    /**
     * Kiểm tra user có phải accountant không
     */
    public function isAccountant()
    {
        return $this->role === 'accountant';
    }

    /**
     * Kiểm tra user có active không
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * Lấy tên role dạng tiếng Việt
     */
    public function getRoleNameAttribute()
    {
        $roles = [
            'admin' => 'Quản trị viên',
            'employee' => 'Nhân viên',
            'accountant' => 'Kế toán',
        ];

        return $roles[$this->role] ?? 'Không xác định';
    }

    /**
     * Lấy trạng thái dạng text
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Hoạt động' : 'Bị khóa';
    }

    /**
     * Lấy badge color cho role
     */
    public function getRoleBadgeAttribute()
    {
        $badges = [
            'admin' => 'danger',
            'employee' => 'primary',
            'accountant' => 'success',
        ];

        return $badges[$this->role] ?? 'secondary';
    }

    /**
     * Scope: Chỉ lấy admin
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope: Chỉ lấy employee
     */
    public function scopeEmployees($query)
    {
        return $query->where('role', 'employee');
    }

    /**
     * Scope: Chỉ lấy user active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // =====================================================
    // SETTINGS METHODS
    // =====================================================

    /**
     * Lấy giá trị setting từ JSON settings
     * 
     * @param string $key - Tên setting cần lấy
     * @param mixed $default - Giá trị mặc định nếu không tìm thấy
     * @return mixed
     */
    public function getSetting($key, $default = null)
    {
        $settings = $this->settings ?? [];
        
        return $settings[$key] ?? $default;
    }

    /**
     * Set giá trị setting vào JSON settings
     * 
     * @param string $key - Tên setting
     * @param mixed $value - Giá trị cần lưu
     * @return bool
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        
        return $this->update(['settings' => $settings]);
    }

    /**
     * Set nhiều settings cùng lúc
     * 
     * @param array $settings - Mảng các settings
     * @return bool
     */
    public function setSettings(array $newSettings)
    {
        $settings = $this->settings ?? [];
        $settings = array_merge($settings, $newSettings);
        
        return $this->update(['settings' => $settings]);
    }

    /**
     * Xóa một setting
     * 
     * @param string $key
     * @return bool
     */
    public function removeSetting($key)
    {
        $settings = $this->settings ?? [];
        unset($settings[$key]);
        
        return $this->update(['settings' => $settings]);
    }

    /**
     * Kiểm tra setting có tồn tại không
     * 
     * @param string $key
     * @return bool
     */
    public function hasSetting($key)
    {
        $settings = $this->settings ?? [];
        
        return isset($settings[$key]);
    }

    // =====================================================
    // EVENTS - Tự động xóa dữ liệu liên quan
    // =====================================================

    protected static function boot()
    {
        parent::boot();
        
        // Khi xóa user, xóa các dữ liệu liên quan (nếu chưa có CASCADE)
        static::deleting(function($user) {
            // Activity logs sẽ tự động xóa (có CASCADE)
            // Leave requests sẽ tự động xóa (có CASCADE)
            // Employee sẽ tự động xóa (có CASCADE)
            
            // Log thông tin trước khi xóa
            \Log::info("Deleting user: {$user->username} (ID: {$user->id})");
        });
    }
}