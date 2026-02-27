<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function update(Request $request, $section)
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];

        // Lưu settings theo section
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, ['_token', '_method'])) {
                $settings[$key] = $value;
            }
        }

        $user->update(['settings' => $settings]);

        return back()->with('success', 'Cài đặt đã được cập nhật!');
    }
}