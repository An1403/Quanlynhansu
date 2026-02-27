<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Danh sách dự án được giao cho nhân viên
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return view('employee.projects.index', ['projects' => collect()]);
        }

        // Lấy danh sách dự án mà nhân viên là thành viên
        $projects = $employee->projects()
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        return view('employee.projects.index', compact('projects'));
    }

    /**
     * Xem chi tiết dự án
     */
    public function show($id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Lấy dự án và kiểm tra nhân viên có phải là thành viên không
        $project = Project::findOrFail($id);

        // Kiểm tra xem nhân viên có trong dự án không
        if (!$employee || !$employee->projects()->where('projects.id', $id)->exists()) {
            abort(403, 'Bạn không có quyền xem dự án này');
        }

        $project->load(['manager', 'team_members']);

        return view('employee.projects.show', compact('project'));
    }
}