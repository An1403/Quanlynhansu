@extends('layouts.admin')

@section('title', 'Thêm Chấm công')

@php
    $pageTitle = 'Thêm Chấm công';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.attendances.index') . '">Chấm công</a> / Thêm mới';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Thêm Chấm công Mới</h1>
    <div class="page-actions">
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.attendances.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Chấm công
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

            <!-- Ngày -->
            <div class="form-group">
                <label for="date">
                    <i class="fas fa-calendar-alt"></i> Ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('date') error @enderror" 
                    id="date" 
                    name="date" 
                    value="{{ old('date', date('Y-m-d')) }}"
                    required>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Giờ vào -->
            <div class="form-group">
                <label for="check_in">
                    <i class="fas fa-sign-in-alt"></i> Giờ vào
                </label>
                <input 
                    type="time" 
                    class="form-control @error('check_in') error @enderror" 
                    id="check_in" 
                    name="check_in" 
                    value="{{ old('check_in') }}"
                    onchange="calculateWorkingHours()">
                @error('check_in')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Giờ ra -->
            <div class="form-group">
                <label for="check_out">
                    <i class="fas fa-sign-out-alt"></i> Giờ ra
                </label>
                <input 
                    type="time" 
                    class="form-control @error('check_out') error @enderror" 
                    id="check_out" 
                    name="check_out" 
                    value="{{ old('check_out') }}"
                    onchange="calculateWorkingHours()">
                @error('check_out')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Số giờ làm -->
            <div class="form-group">
                <label for="working_hours">
                    <i class="fas fa-hourglass-half"></i> Số giờ làm
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="working_hours" 
                    name="working_hours" 
                    value="{{ old('working_hours', 0) }}"
                    step="0.5"
                    min="0"
                    readonly>
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính từ giờ vào và giờ ra
                </small>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-flag"></i> Trạng thái 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status" required>
                    <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Có mặt</option>
                    <option value="Leave" {{ old('status') == 'Leave' ? 'selected' : '' }}>Xin phép</option>
                    <option value="Absent" {{ old('status') == 'Absent' ? 'selected' : '' }}>Vắng mặt</option>
                </select>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Dự án -->
            <div class="form-group">
                <label for="project_id">
                    <i class="fas fa-project-diagram"></i> Dự án
                </label>
                <select class="form-control" id="project_id" name="project_id">
                    <option value="">-- Không có dự án --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">
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
    /**
     * Tính số giờ làm TRỪ 1.5 TIẾNG NGHỈ TRƯA
     */
    function calculateWorkingHours() {
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const workingHoursInput = document.getElementById('working_hours');
        
        if (checkIn && checkOut) {
            // Tạo Date objects để tính
            const checkInTime = new Date(`2000-01-01 ${checkIn}:00`);
            const checkOutTime = new Date(`2000-01-01 ${checkOut}:00`);
            
            if (checkOutTime > checkInTime) {
                // Tính tổng số phút
                const diffMs = checkOutTime - checkInTime;
                const diffMinutes = diffMs / (1000 * 60);
                
                // TRỪ 90 PHÚT NGHỈ TRƯA (1.5 tiếng)
                const workingMinutes = diffMinutes - 90;
                
                // Nếu âm thì set = 0
                if (workingMinutes < 0) {
                    workingHoursInput.value = '0.0';
                } else {
                    // Chuyển sang giờ
                    const workingHours = workingMinutes / 60;
                    workingHoursInput.value = workingHours.toFixed(1);
                }
                
                // Hiển thị thông báo
                updateWorkingHoursDisplay(workingMinutes);
            } else {
                workingHoursInput.value = '0.0';
            }
        } else {
            workingHoursInput.value = '0.0';
        }
    }
    
    /**
     * Hiển thị thông tin chi tiết
     */
    function updateWorkingHoursDisplay(workingMinutes) {
        const checkInValue = document.getElementById('check_in').value;
        const checkOutValue = document.getElementById('check_out').value;
        
        if (checkInValue && checkOutValue) {
            const totalMinutes = calculateTotalMinutes(checkInValue, checkOutValue);
            const hours = Math.floor(workingMinutes / 60);
            const minutes = Math.round(workingMinutes % 60);
            
            console.log(`
                ⏰ Tính toán giờ làm:
                - Giờ vào: ${checkInValue}
                - Giờ ra: ${checkOutValue}
                - Tổng thời gian: ${Math.floor(totalMinutes / 60)}h ${totalMinutes % 60}m
                - Trừ nghỉ trưa: 1.5h (90 phút)
                - Giờ làm thực tế: ${hours}h ${minutes}m
            `);
        }
    }
    
    /**
     * Tính tổng phút từ check_in đến check_out
     */
    function calculateTotalMinutes(checkIn, checkOut) {
        const checkInTime = new Date(`2000-01-01 ${checkIn}:00`);
        const checkOutTime = new Date(`2000-01-01 ${checkOut}:00`);
        const diffMs = checkOutTime - checkInTime;
        return Math.floor(diffMs / (1000 * 60));
    }
    
    // Tự động tính khi load trang (nếu có giá trị cũ)
    document.addEventListener('DOMContentLoaded', function() {
        calculateWorkingHours();
    });

    console.log('✅ Attendance form loaded with lunch break calculation');
</script>
@endpush