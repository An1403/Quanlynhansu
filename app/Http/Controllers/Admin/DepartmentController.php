<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class DepartmentController extends Controller
{
    public function index()
    {
        $departments = DB::table('departments')
            ->leftJoin('employees', 'departments.id', '=', 'employees.department_id')
            ->select(
                'departments.id',
                'departments.name',
                'departments.description',
                'departments.created_at',
                'departments.updated_at',
                DB::raw('COUNT(employees.id) as employee_count')
            )
            ->groupBy('departments.id', 'departments.name', 'departments.description', 'departments.created_at', 'departments.updated_at')
            ->orderBy('departments.created_at', 'desc')
            ->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments|max:100',
            'description' => 'nullable|max:500',
        ]);

        DB::table('departments')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => "Tạo phòng ban mới: {$request->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('admin.departments.index')
            ->with('success', 'Thêm phòng ban thành công!');
    }

    public function show(string $id)
    {
        $department = DB::table('departments')
            ->where('id', $id)
            ->first();
        
        if (!$department) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Không tìm thấy phòng ban!');
        }
        
        $employees = DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->where('employees.department_id', $id)
            ->select(
                'employees.*',
                'positions.name as position_name'
            )
            ->get();

        return view('admin.departments.show', compact('department', 'employees'));
    }

    public function edit(string $id)
    {
        $department = DB::table('departments')
            ->where('id', $id)
            ->first();
        
        if (!$department) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Không tìm thấy phòng ban!');
        }

        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, string $id)
    {
        $oldDepartment = DB::table('departments')->where('id', $id)->first();
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
        ]);

        $updated = DB::table('departments')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_at' => now(),
            ]);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "Cập nhật phòng ban: {$oldDepartment->name} → {$request->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    
        if ($updated) {
            return redirect()->route('admin.departments.index')
                ->with('success', 'Cập nhật phòng ban thành công!');
        }

        return redirect()->route('admin.departments.index')
            ->with('error', 'Cập nhật phòng ban thất bại!');
    }

    public function destroy(string $id)
    {
        $department = DB::table('departments')->where('id', $id)->first();
        // Kiểm tra có nhân viên không
        $count = DB::table('employees')
            ->where('department_id', $id)
            ->count();
        
        if ($count > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', "Không thể xóa! Phòng ban có {$count} nhân viên.");
        }

        $deleted = DB::table('departments')
            ->where('id', $id)
            ->delete();

        if ($deleted) {
            return redirect()->route('admin.departments.index')
                ->with('success', 'Xóa phòng ban thành công!');
        }
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "Xóa phòng ban: {$department->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('admin.departments.index')
            ->with('error', 'Xóa phòng ban thất bại!');
    }
}