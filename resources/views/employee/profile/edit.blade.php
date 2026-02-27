@extends('layouts.employee')

@section('title', 'Chỉnh sửa Hồ sơ cá nhân')

@php
    $pageTitle = 'Chỉnh sửa Hồ sơ cá nhân';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Hồ sơ cá nhân';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Chỉnh sửa Hồ sơ cá nhân</h1>
    <div class="page-actions">
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <!-- Thông tin cơ bản -->
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin cơ bản
        </h3>
        
        <div class="form-grid">
            <!-- Mã nhân viên -->
            <div class="form-group">
                <label for="employee_code">
                    <i class="fas fa-id-card"></i> Mã nhân viên 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="employee_code" 
                    name="employee_code" 
                    value="{{ $employee->employee_code ?? 'N/A' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lock"></i> Mã nhân viên không thể thay đổi
                </small>
            </div>

            <!-- Họ và tên -->
            <div class="form-group">
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

            <!-- Giới tính -->
            <div class="form-group">
                <label for="gender">
                    <i class="fas fa-venus-mars"></i> Giới tính
                </label>
                <select class="form-control" id="gender" name="gender">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" {{ old('gender', $employee->gender ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
                    <option value="Nữ" {{ old('gender', $employee->gender ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                </select>
            </div>

            <!-- Ngày sinh -->
            <div class="form-group">
                <label for="date_of_birth">
                    <i class="fas fa-birthday-cake"></i> Ngày sinh
                </label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="date_of_birth" 
                    name="date_of_birth" 
                    value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
            </div>

            <!-- Số điện thoại -->
            <div class="form-group">
                <label for="phone">
                    <i class="fas fa-phone"></i> Số điện thoại
                </label>
                <input 
                    type="text" 
                    class="form-control @error('phone') error @enderror" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $employee->phone) }}">

                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    class="form-control @error('email') error @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $employee->email) }}">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Địa chỉ -->
            <div class="form-group full-width">
                <label for="address">
                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                </label>
                <textarea 
                    class="form-control" 
                    id="address" 
                    name="address" 
                   rows="3">{{ old('address', $employee->address) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Căn cước công dân -->
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-passport"></i> Thông tin căn cước công dân
        </h3>
        
        <div class="form-grid">
            <!-- Số căn cước -->
            <div class="form-group">
                <label for="identity_card">
                    <i class="fas fa-id-card"></i> Số căn cước công dân
                </label>
                <input 
                    type="text" 
                    class="form-control @error('identity_card') error @enderror" 
                    id="identity_card" 
                    name="identity_card" 
                    value="{{ old('identity_card', $employee->identity_card ?? '') }}" 
                    placeholder="VD: 001094123456"
                    maxlength="20">
                @error('identity_card')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nơi cấp -->
            <div class="form-group">
                <label for="identity_card_issued_at">
                    <i class="fas fa-map-marker-alt"></i> Nơi cấp
                </label>
                <input 
                    type="text" 
                    class="form-control @error('identity_card_issued_at') error @enderror" 
                    id="identity_card_issued_at" 
                    name="identity_card_issued_at" 
                    value="{{ old('identity_card_issued_at', $employee->identity_card_issued_at ?? '') }}" 
                    placeholder="VD: Công an tỉnh Thanh Hoá">
                @error('identity_card_issued_at')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ngày cấp -->
            <div class="form-group">
                <label for="identity_card_date">
                    <i class="fas fa-calendar"></i> Ngày cấp
                </label>
                <input 
                    type="date" 
                    class="form-control @error('identity_card_date') error @enderror" 
                    id="identity_card_date" 
                    name="identity_card_date" 
                     value="{{ old('identity_card_date', $employee->identity_card_date ? $employee->identity_card_date->format('Y-m-d') : '') }}">
                @error('identity_card_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Thông tin công việc (Read-only) -->
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-briefcase"></i> Thông tin công việc
        </h3>
        
        <div class="form-grid">
            <!-- Phòng ban -->
            <div class="form-group">
                <label for="department">
                    <i class="fas fa-building"></i> Phòng ban
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="department" 
                    value="{{ $employee->department->name ?? 'N/A' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lock"></i> Liên hệ quản trị viên để thay đổi
                </small>
            </div>

            <!-- Chức vụ -->
            <div class="form-group">
                <label for="position">
                    <i class="fas fa-user-tie"></i> Chức vụ
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="position" 
                    value="{{ $employee->position->name ?? 'N/A' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lock"></i> Liên hệ quản trị viên để thay đổi
                </small>
            </div>

            <!-- Ngày vào làm -->
            <div class="form-group">
                <label for="join_date">
                    <i class="fas fa-calendar-check"></i> Ngày vào làm
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="join_date" 
                    value="{{ $employee->join_date ? $employee->join_date->format('d/m/Y') : 'N/A' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
            </div>

            <!-- Lương cơ bản -->
            <div class="form-group">
                <label for="base_salary">
                    <i class="fas fa-money-bill-wave"></i> Lương cơ bản (VNĐ)
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="base_salary" 
                    value="{{ $employee->base_salary ? number_format($employee->base_salary, 0, ',', '.') : 'N/A' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lock"></i> Liên hệ quản trị viên để thay đổi
                </small>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-toggle-on"></i> Trạng thái
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="status" 
                    value="{{ $employee->status === 'Active' ? 'Đang làm việc' : 'Đã nghỉ việc' }}" 
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
            </div>
        </div>
    </div>

    <!-- Ảnh đại diện -->
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-image"></i> Ảnh đại diện
        </h3>

        <div class="form-grid">
            <div class="form-group full-width">
                @if($employee->photo)
                    <div style="margin-bottom: 15px;">
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Current photo" style="max-width: 150px; border-radius: 8px; border: 2px solid #e5e7eb;">
                        <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Ảnh hiện tại
                        </p>
                    </div>
                @else
                    <div style="margin-bottom: 15px;">
                        <img src="{{ asset('images/default-avatar.png') }}" alt="Default avatar" style="max-width: 150px; border-radius: 8px; border: 2px solid #e5e7eb;">
                        <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Ảnh mặc định
                        </p>
                    </div>
                @endif
                
                <div class="file-upload" onclick="document.getElementById('photo').click()">
                    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
                    <div class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="file-upload-text">
                        Click để chọn ảnh mới hoặc kéo thả vào đây
                        <br><small>Định dạng: JPG, PNG (tối đa 2MB)</small>
                    </div>
                </div>
                <img id="preview" class="preview-image" style="display: none; margin-top: 15px; max-width: 150px; border-radius: 8px; border: 2px solid #e5e7eb;">
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Cập nhật thông tin</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Preview image
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    console.log('✅ Employee profile edit form loaded');
</script>
@endpush