<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'types_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'request_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot - Tự động tạo mã đơn khi tạo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Tự động sinh mã đơn nếu chưa có
            if (!$model->request_id) {
                $model->request_id = $model->generateRequestId();
            }
        });
    }

    /**
     * Tạo mã đơn tự động
     * Format: LR-YYYYMMDD-HHmmss-UserID
     * Ví dụ: LR-20251206-143022-5
     */
    public function generateRequestId()
    {
        $timestamp = now()->format('YmdHis');
        $userId = auth()->id() ?? 'GUEST';
        
        return 'LR-' . $timestamp . '-' . $userId;
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'types_id');
    }

    /**
     * Scopes
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                     ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Được phép',
            'rejected' => 'Từ chối',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'N/A';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => '#fef3c7',
            'approved' => '#d1fae5',
            'rejected' => '#fee2e2',
        ];

        return $colors[$this->status] ?? '#f3f4f6';
    }

    public function getStatusTextColorAttribute()
    {
        $colors = [
            'pending' => '#b45309',
            'approved' => '#065f46',
            'rejected' => '#991b1b',
        ];

        return $colors[$this->status] ?? '#374151';
    }

    /**
     * Methods - Status Checks
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Methods - Timeline Checks
     */
    public function isExpired()
    {
        return $this->end_date && $this->end_date < Carbon::now();
    }

    public function isUpcoming()
    {
        return $this->start_date && $this->start_date > Carbon::now();
    }

    public function isOngoing()
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }

        $now = Carbon::now();
        return $this->start_date <= $now && $now <= $this->end_date;
    }

    /**
     * Methods - Permissions
     */
    public function canEdit()
    {
        return $this->isPending();
    }

    public function canDelete()
    {
        return $this->isPending();
    }

    public function canApprove()
    {
        return $this->isPending();
    }

    public function canReject()
    {
        return $this->isPending();
    }

    /**
     * Methods - Actions
     */
    public function approve()
    {
        $this->status = 'approved';
        return $this->save();
    }

    public function reject($reason = null)
    {
        $this->status = 'rejected';
        if ($reason) {
            $this->rejected_reason = $reason;
        }
        return $this->save();
    }

    /**
     * Static Methods - Statistics
     */

    
    public static function getPendingRequestsCount()
    {
        return self::pending()->count();
    }

    public static function getApprovedRequestsCount()
    {
        return self::approved()->count();
    }

    public static function getRejectedRequestsCount()
    {
        return self::rejected()->count();
    }

    public static function getUserPendingCount($userId)
    {
        return self::byUser($userId)->pending()->count();
    }

    public static function getUserApprovedCount($userId)
    {
        return self::byUser($userId)->approved()->count();
    }

    public static function getUserRejectedCount($userId)
    {
        return self::byUser($userId)->rejected()->count();
    }
}