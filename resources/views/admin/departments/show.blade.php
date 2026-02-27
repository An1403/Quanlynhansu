@extends('layouts.admin')

@section('title', 'Chi tiết Phòng ban')

@php
    $pageTitle = 'Chi tiết Phòng ban';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.departments.index') . '">Phòng ban</a> / ' . $department->name;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-building"></i> Chi tiết Phòng ban</h1>
    <div class="page-actions">
        <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Department Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Phòng ban
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-building"></i> Tên Phòng ban
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ $department->name }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-users"></i> Số Nhân viên
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 8px; background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="fas fa-users"></i>
                    {{ count($employees) }} nhân viên
                </span>
            </div>
        </div>

        <div class="form-group full-width">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-align-left"></i> Mô tả
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                {{ $department->description ?? 'Không có mô tả' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày tạo
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($department->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-clock"></i> Cập nhật lần cuối
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($department->updated_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Employees in Department -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-users"></i> Danh sách Nhân viên ({{ count($employees) }})
    </h3>

    @if(count($employees) > 0)
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Giới tính</th>
                        <th>Chức vụ</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <div class="employee-info">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="employee-avatar">
                                @else
                                    <div class="employee-placeholder">
                                        {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="employee-name">{{ $employee->full_name }}</div>
                                    <div class="employee-code">{{ $employee->employee_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $employee->gender }}</td>
                        <td>{{ $employee->position_name ?? '-' }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($employee->status === 'Active')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-ban"></i> Resigned</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px 20px;">
            <div style="margin-bottom: 15px; color: #bdc3c7;">
                <i class="fas fa-inbox fa-2x"></i>
            </div>
            <h4 style="color: #111827; margin-bottom: 5px;">Chưa có nhân viên</h4>
            <p style="color: #6b7280;">Phòng ban này hiện chưa có nhân viên nào</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Department show page loaded');
</script>
@endpush