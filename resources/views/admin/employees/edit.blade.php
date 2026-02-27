@extends('layouts.admin')

@section('title', 'Chỉnh sửa Nhân viên')

@php
    $pageTitle = 'Chỉnh sửa Nhân viên';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.employees.index') . '">Nhân viên</a> / Chỉnh sửa';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Chỉnh sửa Nhân viên</h1>
    <div class="page-actions">
        <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i>
            <span>Xem chi tiết</span>
        </a>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
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
                    class="form-control @error('employee_code') error @enderror" 
                    id="employee_code" 
                    name="employee_code" 
                    value="{{ old('employee_code', $employee->employee_code) }}" 
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
                    <option value="Nam" {{ old('gender', $employee->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                    <option value="Nữ" {{ old('gender', $employee->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
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
                    value="{{ old('identity_card', $employee->identity_card) }}" 
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
                    value="{{ old('identity_card_issued_at', $employee->identity_card_issued_at) }}" 
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

    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-briefcase"></i> Thông tin công việc
        </h3>
        
        <div class="form-grid">
            <!-- Phòng ban -->
            <div class="form-group">
                <label for="department_id">
                    <i class="fas fa-building"></i> Phòng ban
                </label>
                <select class="form-control" id="department_id" name="department_id">
                    <option value="">-- Chọn phòng ban --</option>
                    @foreach(\App\Models\Department::all() as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Chức vụ -->
            <div class="form-group">
                <label for="position_id">
                    <i class="fas fa-user-tie"></i> Chức vụ
                </label>
                <select class="form-control" id="position_id" name="position_id">
                    <option value="">-- Chọn chức vụ --</option>
                    @foreach(\App\Models\Position::all() as $pos)
                        <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                            {{ $pos->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ngày vào làm -->
            <div class="form-group">
                <label for="join_date">
                    <i class="fas fa-calendar-check"></i> Ngày vào làm
                </label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="join_date" 
                    name="join_date" 
                    value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}">
            </div>

            <!-- Lương cơ bản -->
            <div class="form-group">
                <label for="base_salary">
                    <i class="fas fa-money-bill-wave"></i> Lương cơ bản (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="base_salary" 
                    name="base_salary" 
                    value="{{ old('base_salary', $employee->base_salary) }}" 
                    min="0"
                    step="100000">
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-toggle-on"></i> Trạng thái
                </label>
                <select class="form-control" id="status" name="status">
                    <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>
                        Đang làm việc
                    </option>
                    <option value="Resigned" {{ old('status', $employee->status) == 'Resigned' ? 'selected' : '' }}>
                        Đã nghỉ việc
                    </option>
                </select>
            </div>

            <!-- Ảnh đại diện -->
            <div class="form-group full-width">
                <label>
                    <i class="fas fa-image"></i> Ảnh đại diện
                </label>
                
                @if($employee->photo)
                    <div style="margin-bottom: 15px;">
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Current photo" style="max-width: 150px; border-radius: 8px; border: 2px solid #e5e7eb;">
                        <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Ảnh hiện tại
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
                <img id="preview" class="preview-image" style="display: none;">
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-secondary">
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

    console.log('✅ Employee edit form loaded');
</script>
@endpush