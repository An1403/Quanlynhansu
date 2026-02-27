<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'location',
        'start_date',
        'end_date',
        'status',
        'description',
        'manager_id',
        'progress',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_SUSPENDED = 'Suspended';

    /**
     * Relationships
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function team_members(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'project_employees',
            'project_id',
            'employee_id'
        )->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS)
                     ->whereDate('end_date', '<=', Carbon::now()->addDays(7))
                     ->whereDate('end_date', '>', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS)
                     ->whereDate('end_date', '<', Carbon::now());
    }

    /**
     * Status Methods
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Lấy số ngày còn lại đến hạn kết thúc
     */
    public function getDaysRemaining(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        $endDate = Carbon::parse($this->end_date);
        $now = Carbon::now();

        if ($endDate < $now) {
            return 0;
        }

        return $endDate->diffInDays($now);
    }

    /**
     * Kiểm tra xem dự án có sắp hết hạn không (trong 7 ngày)
     */
    public function isComingDeadline(): bool
    {
        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining > 0;
    }

    /**
     * Kiểm tra xem dự án đã quá hạn không
     */
    public function isOverdue(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return Carbon::parse($this->end_date) < Carbon::now() && !$this->isCompleted();
    }

    /**
     * Accessors
     */
    public function getFormattedStartDateAttribute(): ?string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : null;
    }

    public function getFormattedEndDateAttribute(): ?string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : null;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        return $this->getDaysRemaining();
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_IN_PROGRESS => 'Đang thực hiện',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_SUSPENDED => 'Tạm dừng',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        $classes = [
            self::STATUS_IN_PROGRESS => 'in-progress',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_SUSPENDED => 'on-hold',
        ];

        return $classes[$this->status] ?? 'in-progress';
    }

    /**
     * Static Methods
     */
    public static function getActiveProjects()
    {
        return self::inProgress()
                   ->orderBy('end_date', 'asc')
                   ->get();
    }

    public static function getUpcomingProjects()
    {
        return self::upcoming()
                   ->orderBy('end_date', 'asc')
                   ->get();
    }

    public static function getOverdueProjects()
    {
        return self::overdue()
                   ->orderBy('end_date', 'asc')
                   ->get();
    }
}