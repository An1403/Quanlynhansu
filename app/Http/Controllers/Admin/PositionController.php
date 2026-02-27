<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    public function index()
    {
        $positions = DB::table('positions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:positions|max:100',
            'allowance' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Tên chức vụ không được để trống',
            'name.unique' => 'Tên chức vụ đã tồn tại',
            'name.max' => 'Tên chức vụ không được vượt quá 100 ký tự',
            'allowance.numeric' => 'Phụ cấp phải là số',
            'allowance.min' => 'Phụ cấp không được âm',
        ]);

        try {
            DB::beginTransaction();

            // Thêm chức vụ
            $positionId = DB::table('positions')->insertGetId([
                'name' => $request->name,
                'allowance' => $request->allowance ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Tạo chức vụ mới: {$request->name} (Phụ cấp: " . number_format($request->allowance ?? 0, 0, ',', '.') . " đ)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.positions.index')
                ->with('success', 'Thêm chức vụ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Position create error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi thêm chức vụ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $position = DB::table('positions')
            ->where('id', $id)
            ->first();
        
        if (!$position) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Không tìm thấy chức vụ!');
        }
        
        $employees = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->where('employees.position_id', $id)
            ->select(
                'employees.*',
                'departments.name as department_name'
            )
            ->get();

        return view('admin.positions.show', compact('position', 'employees'));
    }

    public function edit(string $id)
    {
        $position = DB::table('positions')
            ->where('id', $id)
            ->first();
        
        if (!$position) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Không tìm thấy chức vụ!');
        }

        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:100|unique:positions,name,' . $id,
            'allowance' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Tên chức vụ không được để trống',
            'name.unique' => 'Tên chức vụ đã tồn tại',
            'name.max' => 'Tên chức vụ không được vượt quá 100 ký tự',
            'allowance.numeric' => 'Phụ cấp phải là số',
            'allowance.min' => 'Phụ cấp không được âm',
        ]);

        try {
            DB::beginTransaction();

            // Lấy thông tin cũ để ghi log
            $oldPosition = DB::table('positions')->where('id', $id)->first();
            
            if (!$oldPosition) {
                return redirect()->route('admin.positions.index')
                    ->with('error', 'Không tìm thấy chức vụ!');
            }

            // Cập nhật chức vụ
            DB::table('positions')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'allowance' => $request->allowance ?? 0,
                    'updated_at' => now(),
                ]);

            // Ghi log với thông tin thay đổi
            $changes = [];
            if ($oldPosition->name != $request->name) {
                $changes[] = "Tên: {$oldPosition->name} → {$request->name}";
            }
            if ($oldPosition->allowance != ($request->allowance ?? 0)) {
                $changes[] = "Phụ cấp: " . number_format($oldPosition->allowance, 0, ',', '.') . " → " . number_format($request->allowance ?? 0, 0, ',', '.') . " đ";
            }

            $description = "Cập nhật chức vụ: {$request->name}";
            if (!empty($changes)) {
                $description .= " (" . implode(', ', $changes) . ")";
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.positions.index')
                ->with('success', 'Cập nhật chức vụ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Position update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi cập nhật: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            // Lấy thông tin chức vụ
            $position = DB::table('positions')->where('id', $id)->first();
            
            if (!$position) {
                return redirect()->route('admin.positions.index')
                    ->with('error', 'Không tìm thấy chức vụ!');
            }

            // Kiểm tra có nhân viên nào có chức vụ này không
            $count = DB::table('employees')
                ->where('position_id', $id)
                ->count();
            
            if ($count > 0) {
                return redirect()->route('admin.positions.index')
                    ->with('error', "Không thể xóa! Có {$count} nhân viên đang có chức vụ này.");
            }

            // Xóa chức vụ
            DB::table('positions')->where('id', $id)->delete();

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Xóa chức vụ: {$position->name} (Phụ cấp: " . number_format($position->allowance, 0, ',', '.') . " đ)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.positions.index')
                ->with('success', 'Xóa chức vụ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Position delete error: ' . $e->getMessage());
            
            return redirect()->route('admin.positions.index')
                ->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
}