@extends('layouts.admin')

@section('title', 'Thêm Lương')

@php
    $pageTitle = 'Thêm Lương';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.salaries.index') . '">Lương</a> / Thêm mới';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Thêm Lương Mới</h1>
    <div class="page-actions">
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.salaries.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Lương
        </h3>
        
        <div class="form-grid">
            <!-- Nhân viên -->
            <div class="form-group">
                <label for="employee_id">
                    <i class="fas fa-user"></i> Nhân viên 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('employee_id') error @enderror" id="employee_id" name="employee_id" required>
                    <option value="">-- Chọn nhân viên --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }} ({{ $employee->employee_code }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tháng -->
            <div class="form-group">
                <label for="month">
                    <i class="fas fa-calendar-alt"></i> Tháng 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('month') error @enderror" id="month" name="month" required>
                    <option value="">-- Chọn tháng --</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                            Tháng {{ $m }}
                        </option>
                    @endfor
                </select>
                @error('month')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Năm -->
            <div class="form-group">
                <label for="year">
                    <i class="fas fa-calendar"></i> Năm 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('year') error @enderror" id="year" name="year" required>
                    <option value="">-- Chọn năm --</option>
                    @for ($y = date('Y'); $y >= date('Y') - 10; $y--)
                        <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                @error('year')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tổng giờ làm -->
            <div class="form-group">
                <label for="total_hours">
                    <i class="fas fa-hourglass-half"></i> Tổng giờ làm
                </label>
                <input 
                    type="number" 
                    class="form-control @error('total_hours') error @enderror" 
                    id="total_hours" 
                    name="total_hours" 
                    value="{{ old('total_hours', 0) }}"
                    step="0.5"
                    min="0">
                @error('total_hours')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Lương cơ bản -->
            <div class="form-group">
                <label for="base_salary">
                    <i class="fas fa-money-bill"></i> Lương cơ bản (VNĐ)
                    <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    class="form-control @error('base_salary') error @enderror" 
                    id="base_salary" 
                    name="base_salary" 
                    value="{{ old('base_salary', 0) }}"
                    step="100000"
                    min="0"
                    onchange="calculateTotal()"
                    required>
                @error('base_salary')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phụ cấp -->
            <div class="form-group">
                <label for="allowance">
                    <i class="fas fa-coins"></i> Phụ cấp (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control @error('allowance') error @enderror" 
                    id="allowance" 
                    name="allowance" 
                    value="{{ old('allowance', 0) }}"
                    step="100000"
                    min="0"
                    onchange="calculateTotal()">
                @error('allowance')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Thưởng -->
            <div class="form-group">
                <label for="bonus">
                    <i class="fas fa-gift"></i> Thưởng (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control @error('bonus') error @enderror" 
                    id="bonus" 
                    name="bonus" 
                    value="{{ old('bonus', 0) }}"
                    step="100000"
                    min="0"
                    onchange="calculateTotal()">
                @error('bonus')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Khấu trừ -->
            <div class="form-group">
                <label for="deduction">
                    <i class="fas fa-minus-circle"></i> Khấu trừ (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control @error('deduction') error @enderror" 
                    id="deduction" 
                    name="deduction" 
                    value="{{ old('deduction', 0) }}"
                    step="100000"
                    min="0"
                    onchange="calculateTotal()">
                @error('deduction')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Lương thực -->
            <div class="form-group">
                <label for="total_salary">
                    <i class="fas fa-check-circle"></i> Lương thực (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="total_salary" 
                    name="total_salary" 
                    value="{{ old('total_salary', 0) }}"
                    readonly
                    style="background-color: #dcfce7; font-weight: 600; color: #166534;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính: Lương cơ bản + Phụ cấp + Thưởng - Khấu trừ
                </small>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Lưu thông tin</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function calculateTotal() {
        const baseSalary = parseFloat(document.getElementById('base_salary').value) || 0;
        const allowance = parseFloat(document.getElementById('allowance').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        const deduction = parseFloat(document.getElementById('deduction').value) || 0;
        
        const total = baseSalary + allowance + bonus - deduction;
        document.getElementById('total_salary').value = total;
    }

    console.log('✅ Salary create form loaded');
</script>
@endpush