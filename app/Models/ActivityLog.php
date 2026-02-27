<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope: Lấy hoạt động gần đây nhất
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope: Lọc theo action
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Scope: Lọc theo user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}