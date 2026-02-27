@extends('layouts.admin')

@section('title', 'Chi tiết Chức vụ')

@php
    $pageTitle = 'Chi tiết Chức vụ';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.positions.index') . '">Chức vụ</a> / ' . $position->name;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-briefcase"></i> Chi tiết Chức vụ</h1>
    <div class="page-actions">
        <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Position Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Chức vụ
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-briefcase"></i> Tên Chức vụ
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ $position->name }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-coins"></i> Phụ cấp chức vụ
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 8px; background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 4px; font-weight: 600;">
                    <i class="fas fa-coins"></i>
                    {{ number_format($position->allowance, 0, ',', '.') }} đ
                </span>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày tạo
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($position->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-clock"></i> Cập nhật lần cuối
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($position->updated_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Employees with this Position -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-users"></i> Nhân viên có chức vụ này ({{ count($employees) }})
    </h3>

    @if(count($employees) > 0)
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Lương CB</th>
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
                        <td>{{ $employee->department_name ?? '-' }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ number_format($employee->base_salary, 0, ',', '.') }} đ</td>
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
            <p style="color: #6b7280;">Hiện chưa có nhân viên nào có chức vụ này</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Position show page loaded');
</script>
@endpush