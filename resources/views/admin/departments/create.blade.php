@extends('layouts.admin')

@section('title', 'Thêm Phòng ban')

@php
    $pageTitle = 'Thêm Phòng ban';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.departments.index') . '">Phòng ban</a> / Thêm mới';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Thêm Phòng ban Mới</h1>
    <div class="page-actions">
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.departments.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin cơ bản
        </h3>
        
        <div class="form-grid">
            <!-- Tên Phòng ban -->
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-building"></i> Tên Phòng ban 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('name') error @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="VD: Phòng Kỹ thuật, Phòng Hành chính..."
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Mô tả -->
            <div class="form-group full-width">
                <label for="description">
                    <i class="fas fa-align-left"></i> Mô tả chi tiết
                </label>
                <textarea 
                    class="form-control @error('description') error @enderror" 
                    id="description" 
                    name="description" 
                    rows="6" 
                    placeholder="Nhập mô tả chi tiết về phòng ban, chức năng, trách nhiệm...">{{ old('description') }}</textarea>
                <small style="color: #6b7280; font-size: 12px;">Tối đa 500 ký tự</small>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Lưu thông tin</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    console.log('✅ Department create form loaded');
</script>
@endpush