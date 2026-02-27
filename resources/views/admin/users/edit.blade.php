@extends('layouts.admin')

@section('title', 'Chỉnh sửa Tài khoản')

@php
    $pageTitle = 'Chỉnh sửa Tài khoản';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.users.index') . '">Tài khoản</a> / Chỉnh sửa';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Chỉnh sửa Tài khoản</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    
    <!-- Thông tin cơ bản -->
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin cơ bản
        </h3>
        
        <div class="form-grid">
            <!-- Tên đăng nhập -->
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user-circle"></i> Tên đăng nhập 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('username') error @enderror" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', $user->username) }}" 
                    required>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email 
                    <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    class="form-control @error('email') error @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $employee->email) }}" 
                    required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Họ và tên -->
            <div class="form-group full-width">
                <label for="full_name">
                    <i class="fas fa-user"></i> Họ và tên 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('full_name') error @enderror" 
                    id="full_name" 
                    name="full_name" 
                    value="{{ old('full_name', $employee->full_name) }}" 
                    required>
                @error('full_name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Vai trò và Trạng thái -->
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-shield-alt"></i> Phân quyền và Trạng thái
        </h3>
        
        <div class="form-grid">
            <!-- Vai trò -->
            <div class="form-group">
                <label for="role">
                    <i class="fas fa-shield-check"></i> Vai trò 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('role') error @enderror" id="role" name="role" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                        <i class="fas fa-crown"></i> Quản trị viên
                    </option>
                    <option value="accountant" {{ old('role', $user->role) == 'accountant' ? 'selected' : '' }}>
                        <i class="fas fa-calculator"></i> Kế toán
                    </option>
                    <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>
                        <i class="fas fa-user"></i> Nhân viên
                    </option>
                </select>
                @error('role')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-toggle-on"></i> Trạng thái 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status" required>
                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>
                        <i class="fas fa-check-circle"></i> Hoạt động
                    </option>
                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>
                        <i class="fas fa-ban"></i> Khóa
                    </option>
                </select>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Đổi mật khẩu -->
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-lock"></i> Đổi mật khẩu
        </h3>
        
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 6px; margin-bottom: 20px; color: #92400e; font-size: 13px;">
            <i class="fas fa-info-circle"></i> Để trống nếu không muốn thay đổi mật khẩu
        </div>
        
        <div class="form-grid">
            <!-- Mật khẩu mới -->
            <div class="form-group full-width">
                <label for="password">
                    <i class="fas fa-key"></i> Mật khẩu mới
                </label>
                <input 
                    type="password" 
                    class="form-control @error('password') error @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                    minlength="6">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small style="color: #6b7280; font-size: 12px; margin-top: 5px; display: block;">
                    <i class="fas fa-lock"></i> Mật khẩu phải có ít nhất 6 ký tự
                </small>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="form-group full-width">
                <label for="password_confirmation">
                    <i class="fas fa-key"></i> Xác nhận mật khẩu
                </label>
                <input 
                    type="password" 
                    class="form-control @error('password_confirmation') error @enderror" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Nhập lại mật khẩu để xác nhận"
                    minlength="6">
                @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Nút hành động -->
    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Cập nhật tài khoản</span>
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // Validation for passwords
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password && !passwordConfirmation) {
            e.preventDefault();
            alert('Vui lòng xác nhận mật khẩu');
            return false;
        }
        
        if (password !== passwordConfirmation) {
            e.preventDefault();
            alert('Mật khẩu không trùng khớp');
            return false;
        }
    });

    console.log('✅ User edit form loaded');
</script>
@endpush