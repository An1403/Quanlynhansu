<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Danh sách dự án
     */
    public function index()
    {
        $projects = Project::with('manager')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Form tạo dự án
     */
    public function create()
    {
        $employees = Employee::where('status', 'Active')->get();
        
        return view('admin.projects.create', compact('employees'));
    }

    /**
     * Lưu dự án mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:150',
            'location' => 'nullable|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:In progress,Completed,Suspended',
            'description' => 'nullable',
            'manager_id' => 'nullable|exists:employees,id',
            'progress' => 'nullable|integer|min:0|max:100',
        ], [
            'name.required' => 'Tên dự án không được để trống',
            'name.max' => 'Tên dự án không được vượt quá 150 ký tự',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'manager_id.exists' => 'Quản lý không tồn tại',
            'progress.min' => 'Tiến độ không được âm',
            'progress.max' => 'Tiến độ không được vượt quá 100%',
        ]);

        try {
            DB::beginTransaction();

            // Tạo dự án
            $project = Project::create([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'] ?? 'In progress',
                'description' => $validated['description'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
                'progress' => $validated['progress'] ?? 0,
            ]);

            // Lấy tên quản lý để ghi log
            $managerName = $project->manager ? $project->manager->full_name : 'Chưa phân';

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Tạo dự án mới: {$project->name} (Quản lý: {$managerName}, Địa điểm: {$project->location})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.projects.index')
                ->with('success', 'Thêm dự án thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project create error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi thêm dự án: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xem chi tiết dự án
     */
    public function show($id)
    {
        $project = Project::with(['manager', 'employees'])->findOrFail($id);
        
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Form chỉnh sửa dự án
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $employees = Employee::where('status', 'Active')->get();
        
        return view('admin.projects.edit', compact('project', 'employees'));
    }

    /**
     * Cập nhật dự án
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:150',
            'location' => 'nullable|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:In progress,Completed,Suspended',
            'description' => 'nullable',
            'manager_id' => 'nullable|exists:employees,id',
            'progress' => 'nullable|integer|min:0|max:100',
        ], [
            'name.required' => 'Tên dự án không được để trống',
            'name.max' => 'Tên dự án không được vượt quá 150 ký tự',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'manager_id.exists' => 'Quản lý không tồn tại',
            'progress.min' => 'Tiến độ không được âm',
            'progress.max' => 'Tiến độ không được vượt quá 100%',
        ]);

        try {
            DB::beginTransaction();

            // Lưu thông tin cũ để so sánh
            $oldData = [
                'name' => $project->name,
                'status' => $project->status,
                'progress' => $project->progress,
                'manager_id' => $project->manager_id,
                'location' => $project->location,
            ];

            // Cập nhật dự án
            $project->update([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'] ?? 'In progress',
                'description' => $validated['description'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
                'progress' => $validated['progress'] ?? 0,
            ]);

            // Ghi log chi tiết các thay đổi
            $changes = [];
            
            if ($oldData['name'] != $validated['name']) {
                $changes[] = "Tên: {$oldData['name']} → {$validated['name']}";
            }
            
            if ($oldData['status'] != ($validated['status'] ?? 'In progress')) {
                $changes[] = "Trạng thái: {$oldData['status']} → " . ($validated['status'] ?? 'In progress');
            }
            
            if ($oldData['progress'] != ($validated['progress'] ?? 0)) {
                $changes[] = "Tiến độ: {$oldData['progress']}% → " . ($validated['progress'] ?? 0) . "%";
            }
            
            if ($oldData['manager_id'] != ($validated['manager_id'] ?? null)) {
                $oldManager = $oldData['manager_id'] ? Employee::find($oldData['manager_id'])->full_name : 'Chưa phân';
                $newManager = $validated['manager_id'] ? Employee::find($validated['manager_id'])->full_name : 'Chưa phân';
                $changes[] = "Quản lý: {$oldManager} → {$newManager}";
            }
            
            if ($oldData['location'] != ($validated['location'] ?? null)) {
                $changes[] = "Địa điểm: " . ($oldData['location'] ?? 'N/A') . " → " . ($validated['location'] ?? 'N/A');
            }

            $description = "Cập nhật dự án: {$validated['name']}";
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

            return redirect()->route('admin.projects.index')
                ->with('success', 'Cập nhật dự án thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi cập nhật: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa dự án
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        try {
            DB::beginTransaction();

            // Lưu thông tin trước khi xóa
            $projectName = $project->name;
            $projectLocation = $project->location;
            $managerName = $project->manager ? $project->manager->full_name : 'Chưa phân';

            // Xóa dự án
            $project->delete();

            // Ghi log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Xóa dự án: {$projectName} (Quản lý: {$managerName}, Địa điểm: {$projectLocation})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.projects.index')
                ->with('success', 'Xóa dự án thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project delete error: ' . $e->getMessage());
            
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
}