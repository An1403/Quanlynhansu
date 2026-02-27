<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalariesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select(
                'salaries.*',
                'employees.full_name',
                'employees.employee_code'
            )
            ->orderBy('salaries.year', 'desc')
            ->orderBy('salaries.month', 'desc')
            ->paginate(15);

        return view('admin.salaries.index', compact('salaries'));
    }

    public function create()
    {
        $employees = DB::table('employees')
            ->where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        return view('admin.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'total_hours' => 'nullable|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
        ]);

        // Calculate total salary
        $baseSalary = (float)$request->base_salary;
        $allowance = (float)($request->allowance ?? 0);
        $bonus = (float)($request->bonus ?? 0);
        $deduction = (float)($request->deduction ?? 0);
        $totalSalary = ($baseSalary + $allowance + $bonus) - $deduction;

        // Check if salary already exists
        $exists = DB::table('salaries')
            ->where('employee_id', $request->employee_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bản ghi lương cho tháng/năm này đã tồn tại!');
        }

        try {
            DB::beginTransaction();

            // Lấy thông tin nhân viên để ghi log
            $employee = DB::table('employees')->where('id', $request->employee_id)->first();

            // Insert salary
            DB::table('salaries')->insert([
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
                'total_hours' => $request->total_hours ?? 0,
                'base_salary' => $baseSalary,
                'allowance' => $allowance,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'total_salary' => $totalSalary,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'description' => "Tạo bảng lương: {$employee->full_name} ({$employee->employee_code}) - Tháng {$request->month}/{$request->year} - Tổng lương: " . number_format($totalSalary, 0, ',', '.') . " đ",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', 'Thêm bản ghi lương thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary create error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi thêm bản ghi lương: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $salary = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->where('salaries.id', $id)
            ->select(
                'salaries.*',
                'employees.full_name',
                'employees.employee_code'
            )
            ->first();

        if (!$salary) {
            return redirect()->route('admin.salaries.index')
                ->with('error', 'Không tìm thấy bản ghi lương!');
        }

        return view('admin.salaries.show', compact('salary'));
    }

    public function edit(string $id)
    {
        $salary = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->where('salaries.id', $id)
            ->select(
                'salaries.*',
                'employees.full_name',
                'employees.employee_code'
            )
            ->first();

        if (!$salary) {
            return redirect()->route('admin.salaries.index')
                ->with('error', 'Không tìm thấy bản ghi lương!');
        }

        $employees = DB::table('employees')
            ->orderBy('full_name')
            ->get();

        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $salary = DB::table('salaries')->where('id', $id)->first();

        if (!$salary) {
            return redirect()->route('admin.salaries.index')
                ->with('error', 'Không tìm thấy bản ghi lương!');
        }

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'total_hours' => 'nullable|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
        ]);

        // Calculate total salary
        $baseSalary = (float)$request->base_salary;
        $allowance = (float)($request->allowance ?? 0);
        $bonus = (float)($request->bonus ?? 0);
        $deduction = (float)($request->deduction ?? 0);
        $totalSalary = ($baseSalary + $allowance + $bonus) - $deduction;

        try {
            DB::beginTransaction();

            // Lấy thông tin nhân viên để ghi log
            $employee = DB::table('employees')->where('id', $salary->employee_id)->first();

            // Lưu giá trị cũ để so sánh
            $oldTotalSalary = $salary->total_salary;
            $oldMonth = $salary->month;
            $oldYear = $salary->year;

            // Update salary
            DB::table('salaries')
                ->where('id', $id)
                ->update([
                    'month' => $request->month,
                    'year' => $request->year,
                    'total_hours' => $request->total_hours ?? 0,
                    'base_salary' => $baseSalary,
                    'allowance' => $allowance,
                    'bonus' => $bonus,
                    'deduction' => $deduction,
                    'total_salary' => $totalSalary,
                    'updated_at' => now(),
                ]);

            // ✅ Ghi Activity Log - Chi tiết những gì thay đổi
            $changes = [];
            
            if ($oldMonth != $request->month || $oldYear != $request->year) {
                $changes[] = "Thời gian: {$oldMonth}/{$oldYear} → {$request->month}/{$request->year}";
            }
            
            if ($oldTotalSalary != $totalSalary) {
                $changes[] = "Tổng lương: " . number_format($oldTotalSalary, 0, ',', '.') . " đ → " . number_format($totalSalary, 0, ',', '.') . " đ";
            }

            $changeDescription = !empty($changes) ? ' (' . implode(', ', $changes) . ')' : '';

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => "Cập nhật bảng lương: {$employee->full_name} ({$employee->employee_code}) - Tháng {$request->month}/{$request->year}" . $changeDescription,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', 'Cập nhật bản ghi lương thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary update error: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Lỗi cập nhật bản ghi lương: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $salary = DB::table('salaries')->where('id', $id)->first();

        if (!$salary) {
            return redirect()->route('admin.salaries.index')
                ->with('error', 'Không tìm thấy bản ghi lương!');
        }

        try {
            DB::beginTransaction();

            // Lấy thông tin nhân viên để ghi log
            $employee = DB::table('employees')->where('id', $salary->employee_id)->first();

            // Delete salary
            DB::table('salaries')->where('id', $id)->delete();

            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Xóa bảng lương: {$employee->full_name} ({$employee->employee_code}) - Tháng {$salary->month}/{$salary->year} - Tổng lương: " . number_format($salary->total_salary, 0, ',', '.') . " đ",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', 'Xóa bản ghi lương thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary delete error: ' . $e->getMessage());
            
            return redirect()->route('admin.salaries.index')
                ->with('error', 'Lỗi xóa bản ghi lương: ' . $e->getMessage());
        }
    }
    
    /**
     * Xuất file Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ], [
            'month.required' => 'Vui lòng chọn tháng',
            'year.required' => 'Vui lòng chọn năm',
        ]);

        $month = $request->month;
        $year = $request->year;

        // Kiểm tra có dữ liệu không
        $count = DB::table('salaries')
            ->where('month', $month)
            ->where('year', $year)
            ->count();

        if ($count == 0) {
            return back()->with('error', "Không có dữ liệu lương tháng {$month}/{$year}");
        }

        try {
            // ✅ Ghi Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'export',
                'description' => "Xuất file Excel bảng lương tháng {$month}/{$year} ({$count} bản ghi)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Tạo Excel
            $export = new SalariesExport($month, $year);
            $spreadsheet = $export->generate();
            
            // Tên file
            $fileName = 'Bang_Luong_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . $year . '.xlsx';
            
            // Tạo writer
            $writer = new Xlsx($spreadsheet);
            
            // Set headers để download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            // Output file
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            \Log::error('Export Excel error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi xuất file: ' . $e->getMessage());
        }
    }
}