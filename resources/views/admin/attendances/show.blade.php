@extends('layouts.admin')

@section('title', 'Chi tiết Chấm công')

@php
    $pageTitle = 'Chi tiết Chấm công';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.attendances.index') . '">Chấm công</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clock"></i> Chi tiết Chấm công</h1>
    <div class="page-actions">
        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Attendance Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Chấm công
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-user"></i> Nhân viên
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <div class="employee-info">
                    @if($attendance->photo)
                        <img src="{{ asset('storage/' . $attendance->photo) }}" alt="{{ $attendance->full_name }}" class="employee-avatar">
                    @else
                        <div class="employee-placeholder">
                            {{ strtoupper(substr($attendance->full_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="employee-name">{{ $attendance->full_name }}</div>
                        <div class="employee-code">{{ $attendance->employee_code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-sign-in-alt"></i> Giờ vào
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-sign-out-alt"></i> Giờ ra
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-hourglass-half"></i> Số giờ làm
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 8px; background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 4px; font-weight: 600;">
                    <i class="fas fa-hourglass-half"></i>
                    {{ $attendance->working_hours }} giờ
                </span>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-flag"></i> Trạng thái
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                @if($attendance->status === 'Present')
                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Có mặt</span>
                @elseif($attendance->status === 'Leave')
                    <span class="badge" style="background: #fef3c7; color: #b45309;">
                        <i class="fas fa-file-alt"></i> Xin phép
                    </span>
                @else
                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Vắng mặt</span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-project-diagram"></i> Dự án
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $attendance->project_name ?? '-' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày tạo
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($attendance->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-clock"></i> Cập nhật lần cuối
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($attendance->updated_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Attendance show page loaded');
</script>
@endpush