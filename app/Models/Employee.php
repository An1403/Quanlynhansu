<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'gender',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'photo',
        'identity_card',
        'identity_card_issued_at',
        'identity_card_date',
        'department_id',
        'position_id',
        'join_date',
        'base_salary',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'identity_card_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'Active';
    const STATUS_RESIGNED = 'Resigned';

    /**
     * Lấy thông tin user của nhân viên
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lấy thông tin phòng ban
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
/**
 * Relationship - Danh sách dự án mà nhân viên quản lý
 */
    public function managed_projects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /**
     * Lấy thông tin chức vụ
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Lấy danh sách chấm công
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    /**
     * Lấy danh sách đơn xin nghỉ qua user relationship
     */
    public function leaveRequests()
    {
        // LeaveRequests liên kết qua user, không phải trực tiếp
        return $this->hasMany(LeaveRequest::class, 'user_id', 'user_id');
    }

    /**
     * Lấy danh sách lương
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class, 'employee_id');
    }

    /**
     * Lấy danh sách dự án được giao
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_employees', 'employee_id', 'project_id')
                    ->withTimestamps();
    }

    

    /**
     * Kiểm tra nhân viên đang hoạt động
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Kiểm tra nhân viên đã từng bỏ việc
     */
    public function hasResigned()
    {
        return $this->status === self::STATUS_RESIGNED;
    }

    /**
     * Lấy tên phòng ban
     */
    public function getDepartmentNameAttribute()
    {
        return $this->department?->name ?? 'Chưa phân công';
    }

    /**
     * Lấy tên chức vụ
     */
    public function getPositionNameAttribute()
    {
        return $this->position?->name ?? 'Chưa cấp';
    }

    /**
     * Accessor lấy tên đầy đủ
     */
    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] ?? '';
    }

    /**
     * Format ngày sinh
     */
    public function getFormattedDateOfBirthAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->format('d/m/Y') : null;
    }

    /**
     * Format ngày vào công ty
     */
    public function getFormattedJoinDateAttribute()
    {
        return $this->join_date ? $this->join_date->format('d/m/Y') : null;
    }
    
    
}