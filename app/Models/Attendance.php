<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
class Attendance extends Model
{
    use HasFactory;

    /**
     * Tên bảng
     */
    protected $table = 'attendances';

    /**
     * Các cột có thể gán hàng loạt
     */
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'status',
        'project_id',
        'notes',  // ✅ Thêm dòng này
    ];

    /**
     * Các cột nên được cast
     */
    protected $casts = [
        'date' => 'date',
        'working_hours' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Attendance thuộc về Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Attendance thuộc về Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope: Lọc theo nhân viên
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope: Lọc theo ngày
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope: Lọc theo dãy ngày
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope: Lọc theo trạng thái
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Lọc theo dự án
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope: Lấy các bản ghi có mặt
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    /**
     * Scope: Lấy các bản ghi xin phép
     */
    public function scopeLeave($query)
    {
        return $query->where('status', 'Leave');
    }

    /**
     * Scope: Lấy các bản ghi vắng mặt
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    /**
     * Scope: Sắp xếp theo ngày mới nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Lấy trạng thái hiển thị
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'Present' => 'Có mặt',
            'Leave' => 'Xin phép',
            'Absent' => 'Vắng mặt',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Lấy ngày dạng dd/mm/yyyy
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Lấy giờ vào dạng hh:mm
     */
    public function getFormattedCheckInAttribute()
{
    if (!$this->check_in) {
        return '-';
    }
    
    try {
        // Nếu đã là Carbon object
        if ($this->check_in instanceof \Carbon\Carbon) {
            return $this->check_in->format('H:i');
        }
        
        // Nếu là string TIME
        $time = \Carbon\Carbon::createFromFormat('H:i:s', $this->check_in);
        return $time->format('H:i');
    } catch (\Exception $e) {
        return $this->check_in;
    }
}

    /**
     * Lấy giờ ra dạng hh:mm
     */
    public function getFormattedCheckOutAttribute()
{
    if (!$this->check_out) {
        return '-';
    }
    
    try {
        if ($this->check_out instanceof \Carbon\Carbon) {
            return $this->check_out->format('H:i');
        }
        
        $time = \Carbon\Carbon::createFromFormat('H:i:s', $this->check_out);
        return $time->format('H:i');
    } catch (\Exception $e) {
        return $this->check_out;
    }
}

    /**
     * Methods
     */
public function calculateWorkingHours()
{
    if ($this->check_in && $this->check_out) {
        try {
            // Parse từ TIME string (vì database lưu TIME)
            $checkInTime = \Carbon\Carbon::createFromFormat('H:i:s', $this->check_in);
            $checkOutTime = \Carbon\Carbon::createFromFormat('H:i:s', $this->check_out);
            
            if ($checkOutTime > $checkInTime) {
                $totalMinutes = $checkOutTime->diffInMinutes($checkInTime);
                $workingMinutes = $totalMinutes - 90; // Trừ 1.5 tiếng
                
                if ($workingMinutes < 0) {
                    return 0;
                }
                
                $this->working_hours = round($workingMinutes / 60, 2);
                return $this->working_hours;
            }
        } catch (\Exception $e) {
            \Log::error('Calculate working hours error: ' . $e->getMessage());
        }
    }
    
    return 0;
}

    /**
     * ✅ Tự động tính giờ làm khi save - FIX VERSION
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($attendance) {
            \Log::info('💾 SAVING EVENT START', [
                'working_hours_input' => $attendance->working_hours,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
            ]);
            
            // ✅ QUAN TRỌNG: CHỈ tính nếu working_hours THẬT SỰ là 0 hoặc null
            // Không dùng == vì nó coi 0.0 và null là giống nhau
            $hasWorkingHours = $attendance->working_hours !== null && $attendance->working_hours > 0;
            
            \Log::info('🔍 Check conditions', [
                'has_working_hours' => $hasWorkingHours,
                'working_hours_value' => $attendance->working_hours,
                'is_null' => $attendance->working_hours === null,
                'is_greater_than_zero' => $attendance->working_hours > 0,
            ]);
            
            // Nếu ĐÃ CÓ working_hours từ Controller → KHÔNG tính lại
            if ($hasWorkingHours) {
                \Log::info('✅ Using working_hours from Controller', [
                    'working_hours' => $attendance->working_hours
                ]);
                return; // ← RETURN NGAY, không làm gì cả
            }
            
            // Chỉ tính nếu CHƯA CÓ working_hours NHƯNG CÓ check_in và check_out
            if ($attendance->check_in && $attendance->check_out) {
                try {
                    // Parse TIME string
                    $checkInStr = is_string($attendance->check_in) 
                        ? $attendance->check_in 
                        : $attendance->check_in->format('H:i:s');
                        
                    $checkOutStr = is_string($attendance->check_out) 
                        ? $attendance->check_out 
                        : $attendance->check_out->format('H:i:s');
                    
                    $checkInTime = \Carbon\Carbon::createFromFormat('H:i:s', $checkInStr);
                    $checkOutTime = \Carbon\Carbon::createFromFormat('H:i:s', $checkOutStr);
                    
                    if ($checkOutTime > $checkInTime) {
                        $totalMinutes = $checkOutTime->diffInMinutes($checkInTime);
                        $workingMinutes = $totalMinutes - 90; // Trừ 1.5h
                        
                        $attendance->working_hours = $workingMinutes > 0 ? round($workingMinutes / 60, 2) : 0;
                        
                        \Log::info('🤖 Model auto-calculated', [
                            'working_hours' => $attendance->working_hours
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('❌ Boot calculate error: ' . $e->getMessage());
                }
            }
        });
    }
    

    /**
     * Kiểm tra xem có mặt hay không
     */
    public function isPresent()
    {
        return $this->status === 'Present';
    }

    /**
     * Kiểm tra xem có xin phép hay không
     */
    public function isOnLeave()
    {
        return $this->status === 'Leave';
    }

    /**
     * Kiểm tra xem có vắng mặt hay không
     */
    public function isAbsent()
    {
        return $this->status === 'Absent';
    }

    /**
     * Lấy tổng giờ làm của nhân viên trong tháng
     */
    public static function getTotalHoursByEmployeeInMonth($employeeId, $month, $year)
    {
        return self::byEmployee($employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('working_hours');
    }

    /**
     * Lấy số ngày có mặt của nhân viên trong tháng
     */
    public static function getPresentDaysByEmployeeInMonth($employeeId, $month, $year)
    {
        return self::byEmployee($employeeId)
            ->present()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();
    }

    /**
     * Lấy số ngày vắng mặt của nhân viên trong tháng
     */
    public static function getAbsentDaysByEmployeeInMonth($employeeId, $month, $year)
    {
        return self::byEmployee($employeeId)
            ->absent()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();
    }

    /**
     * Lấy số ngày xin phép của nhân viên trong tháng
     */
    public static function getLeaveDaysByEmployeeInMonth($employeeId, $month, $year)
    {
        return self::byEmployee($employeeId)
            ->leave()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();
    }
}