@extends('layouts.admin')

@section('title', 'Phân quyền Tài khoản')

@php
    $pageTitle = 'Phân quyền Tài khoản';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.users.index') . '">Tài khoản</a> / Phân quyền';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-shield-check"></i> Quản lý Phân quyền</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #10b981; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #1e40af; font-size: 14px;">
    <i class="fas fa-info-circle"></i> Cập nhật vai trò cho các tài khoản người dùng
</div>

<!-- Mô tả các vai trò -->
<div class="card">
    <h3 style="margin: 0 0 20px 0; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-list"></i> Mô tả các vai trò
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
            <h4 style="margin: 0 0 8px 0; color: #111827; font-size: 14px;">
                <i class="fas fa-crown"></i> Quản trị viên
            </h4>
            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.5;">
                Có quyền truy cập toàn bộ hệ thống, quản lý tất cả dữ liệu và người dùng
            </p>
        </div>
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
            <h4 style="margin: 0 0 8px 0; color: #111827; font-size: 14px;">
                <i class="fas fa-user"></i> Nhân viên
            </h4>
            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.5;">
                Có quyền xem thông tin cá nhân, chấm công, đơn xin nghỉ của mình
            </p>
        </div>
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
            <h4 style="margin: 0 0 8px 0; color: #111827; font-size: 14px;">
                <i class="fas fa-calculator"></i> Kế toán
            </h4>
            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.5;">
                Có quyền quản lý lương, tính toán tiền và báo cáo tài chính
            </p>
        </div>
    </div>
</div>

<!-- Bảng phân quyền -->
<div class="card" style="margin-top: 20px;">
    <div class="table-container">
        @if($users->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Tài khoản</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò hiện tại</th>
                        <th>Thay đổi vai trò</th>
                        <th style="text-align: center;">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="employee-name">{{ $user->username }}</div>
                                    <div class="employee-code">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->full_name ?? '-' }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>
                            <span class="badge" style="
                                @if($user->role == 'admin')
                                    background: #fee2e2; color: #991b1b;
                                @elseif($user->role == 'accountant')
                                    background: #fef3c7; color: #92400e;
                                @else
                                    background: #dbeafe; color: #1e40af;
                                @endif
                            ">
                                @if($user->role == 'admin')
                                    <i class="fas fa-crown"></i> Quản trị viên
                                @elseif($user->role == 'accountant')
                                    <i class="fas fa-calculator"></i> Kế toán
                                @else
                                    <i class="fas fa-user"></i> Nhân viên
                                @endif
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                <select name="role" class="filter-select" onchange="this.form.submit();" style="min-width: 150px;">
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                        Quản trị viên
                                    </option>
                                    <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>
                                        Nhân viên
                                    </option>
                                    <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>
                                        Kế toán
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon btn-edit" title="Chi tiết">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-slash fa-2x"></i>
                </div>
                <h3>Chưa có tài khoản</h3>
                <p>Hiện chưa có tài khoản nào trong hệ thống</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Users permissions loaded');
</script>
@endpush