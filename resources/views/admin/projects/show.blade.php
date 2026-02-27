@extends('layouts.admin')

@section('title', 'Chi tiết Dự án')

@php
    $pageTitle = 'Chi tiết Dự án';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.projects.index') . '">Dự án</a> / ' . $project->name;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <style>
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin: 8px 0;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 4px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 6px;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-project-diagram"></i> Chi tiết Dự án</h1>
    <div class="page-actions">
        <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Project Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Dự án
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-project-diagram"></i> Tên Dự án
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ $project->name }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-user-tie"></i> Quản lý Dự án
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                @if($project->manager)
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600;">
                            {{ substr($project->manager->full_name, 0, 1) }}
                        </div>
                        <div>
                            <div style="color: #111827; font-weight: 500;">{{ $project->manager->full_name }}</div>
                            <div style="color: #6b7280; font-size: 12px;">{{ $project->manager->position->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                @else
                    <span style="color: #9ca3af;">-</span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-flag"></i> Trạng thái
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                @if($project->status === 'In progress')
                    <span class="badge" style="background: #fef3c7; color: #b45309; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-spinner fa-spin"></i> Đang thực hiện
                    </span>
                @elseif($project->status === 'Completed')
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Hoàn thành
                    </span>
                @else
                    <span class="badge badge-danger">
                        <i class="fas fa-pause-circle"></i> Tạm dừng
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-chart-pie"></i> Tiến độ
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <div class="progress-label">
                    <span style="color: #374151; font-weight: 500;">Tiến độ dự án</span>
                    <span style="color: #3b82f6; font-weight: 600;">{{ $project->progress ?? 0 }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $project->progress ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-map-marker-alt"></i> Địa điểm
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $project->location ?? '-' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-check"></i> Ngày bắt đầu
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $project->formatted_start_date ?? '-' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-times"></i> Ngày kết thúc
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $project->formatted_end_date ?? '-' }}
            </div>
        </div>

        <div class="form-group full-width">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-align-left"></i> Mô tả
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                {{ $project->description ?? 'Không có mô tả' }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày tạo
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $project->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-clock"></i> Cập nhật lần cuối
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $project->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Team Members -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-users"></i> Thành viên dự án ({{ $project->team_members->count() }})
    </h3>

    @if($project->team_members->count() > 0)
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Chức vụ</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->team_members as $employee)
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
                        <td>{{ $employee->department?->name ?? '-' }}</td>
                        <td>{{ $employee->position?->name ?? '-' }}</td>
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
            <h4 style="color: #111827; margin-bottom: 5px;">Chưa có thành viên</h4>
            <p style="color: #6b7280;">Dự án này hiện chưa có nhân viên nào tham gia</p>
        </div>
    @endif
</div>

@endsection