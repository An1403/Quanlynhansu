<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
trait LogsActivity
{
    /**
     * Ghi nhật ký hoạt động
     */
    public static function logActivity($action, $description = null, $userId = null)
    {
        try {
            ActivityLog::create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging activity: ' . $e->getMessage());
        }
    }
}