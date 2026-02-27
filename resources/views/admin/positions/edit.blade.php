@extends('layouts.admin')

@section('title', 'Sửa Chức vụ')

@php
    $pageTitle = 'Sửa Chức vụ';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.positions.index') . '">Chức vụ</a> / Sửa';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit"></i> Sửa Chức vụ: {{ $position->name }}</h1>
    <div class="page-actions">
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Chức vụ
        </h3>
        
        <div class="form-grid">
            <!-- Tên Chức vụ -->
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-briefcase"></i> Tên Chức vụ 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('name') error @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $position->name) }}" 
                    placeholder="VD: Quản lý Dự án, Kỹ sư Phần mềm..."
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phụ cấp -->
            <div class="form-group">
                <label for="allowance">
                    <i class="fas fa-coins"></i> Phụ cấp chức vụ (VNĐ)
                </label>
                <input 
                    type="number" 
                    class="form-control @error('allowance') error @enderror" 
                    id="allowance" 
                    name="allowance" 
                    value="{{ old('allowance', $position->allowance) }}" 
                    placeholder="VD: 1000000"
                    min="0"
                    step="100000">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Phụ cấp sẽ được cộng vào lương của nhân viên có chức vụ này
                </small>
                @error('allowance')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-history"></i> Thông tin hệ thống
        </h3>
        
        <div class="form-grid">
            <!-- Ngày tạo -->
            <div class="form-group">
                <label>
                    <i class="fas fa-calendar-alt"></i> Ngày tạo
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    value="{{ \Carbon\Carbon::parse($position->created_at)->format('d/m/Y H:i') }}" 
                    disabled>
            </div>

            <!-- Cập nhật lần cuối -->
            <div class="form-group">
                <label>
                    <i class="fas fa-clock"></i> Cập nhật lần cuối
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    value="{{ \Carbon\Carbon::parse($position->updated_at)->format('d/m/Y H:i') }}" 
                    disabled>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
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
    console.log('✅ Position edit form loaded');
</script>
@endpush