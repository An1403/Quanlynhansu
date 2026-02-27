@extends('layouts.employee')

@section('title', 'Chi tiết Chấm công')

@php
    $pageTitle = 'Chi tiết Chấm công';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.attendance.index') . '">Chấm công</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-eye"></i> Chi tiết Chấm công</h1>
    <div class="page-actions">
        <a href="{{ route('employee.attendance.edit', $attendance->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            <span>Chỉnh sửa</span>
        </a>
        <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Chấm công
    </h3>

    <div class="form-grid">
        <!-- Ngày -->
        <div class="form-group">
            <label><i class="fas fa-calendar-alt"></i> Ngày</label>
            <input type="text" class="form-control" 
                   value="{{ $attendance->formatted_date }}" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Giờ vào -->
        <div class="form-group">
            <label><i class="fas fa-sign-in-alt"></i> Giờ vào</label>
            <input type="text" class="form-control" 
                   value="{{ $attendance->formatted_check_in }}" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Giờ ra -->
        <div class="form-group">
            <label><i class="fas fa-sign-out-alt"></i> Giờ ra</label>
            <input type="text" class="form-control" 
                   value="{{ $attendance->formatted_check_out }}" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Số giờ làm -->
        <div class="form-group">
            <label><i class="fas fa-hourglass-half"></i> Số giờ làm</label>
            <input type="text" class="form-control" 
                   value="{{ number_format($attendance->working_hours, 1) }} giờ" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Trạng thái -->
        <div class="form-group">
            <label><i class="fas fa-flag"></i> Trạng thái</label>
            <input type="text" class="form-control" 
                   value="{{ $attendance->status_label }}" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Dự án -->
        <div class="form-group">
            <label><i class="fas fa-project-diagram"></i> Dự án</label>
            <input type="text" class="form-control" 
                   value="{{ $attendance->project->name ?? '-' }}" 
                   readonly style="background: #f9fafb; cursor: not-allowed;">
        </div>

        <!-- Ghi chú -->
        <div class="form-group full-width">
            <label><i class="fas fa-sticky-note"></i> Ghi chú</label>
            <textarea class="form-control" rows="3" 
                      readonly style="background: #f9fafb; cursor: not-allowed;">{{ $attendance->notes ?? '-' }}</textarea>
        </div>
    </div>
</div>

<div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
    <form action="{{ route('employee.attendance.destroy', $attendance->id) }}" method="POST" 
          style="display: inline;" 
          onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi chấm công này?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i>
            <span>Xóa</span>
        </button>
    </form>
    <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        <span>Quay lại</span>
    </a>
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Attendance show loaded');
</script>
@endpush