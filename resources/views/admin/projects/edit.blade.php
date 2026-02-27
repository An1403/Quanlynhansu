@extends('layouts.admin')

@section('title', 'Sửa Dự án')

@php
    $pageTitle = 'Sửa Dự án';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.projects.index') . '">Dự án</a> / Sửa';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit"></i> Sửa Dự án: {{ $project->name }}</h1>
    <div class="page-actions">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Dự án
        </h3>
        
        <div class="form-grid">
            <!-- Tên Dự án -->
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-project-diagram"></i> Tên Dự án 
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('name') error @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $project->name) }}" 
                    placeholder="VD: Dự án xây dựng nhà cao tầng..."
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Quản lý Dự án -->
            <div class="form-group">
                <label for="manager_id">
                    <i class="fas fa-user-tie"></i> Quản lý Dự án
                </label>
                <select 
                    class="form-control @error('manager_id') error @enderror" 
                    id="manager_id" 
                    name="manager_id">
                    <option value="">-- Chọn quản lý --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('manager_id', $project->manager_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('manager_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Địa điểm -->
            <div class="form-group">
                <label for="location">
                    <i class="fas fa-map-marker-alt"></i> Địa điểm
                </label>
                <input 
                    type="text" 
                    class="form-control @error('location') error @enderror" 
                    id="location" 
                    name="location" 
                    value="{{ old('location', $project->location) }}" 
                    placeholder="VD: Quận 1, TP. Hồ Chí Minh">
                @error('location')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ngày bắt đầu -->
            <div class="form-group">
                <label for="start_date">
                    <i class="fas fa-calendar-check"></i> Ngày bắt đầu
                </label>
                <input 
                    type="date" 
                    class="form-control @error('start_date') error @enderror" 
                    id="start_date" 
                    name="start_date" 
                    value="{{ old('start_date', $project->start_date?->format('Y-m-d'))}}">
                @error('start_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ngày kết thúc -->
            <div class="form-group">
                <label for="end_date">
                    <i class="fas fa-calendar-times"></i> Ngày kết thúc
                </label>
                <input 
                    type="date" 
                    class="form-control @error('end_date') error @enderror" 
                    id="end_date" 
                    name="end_date" 
                    value="{{ old('end_date', $project->end_date?->format('Y-m-d'))}}">
                @error('end_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-flag"></i> Trạng thái
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status">
                    <option value="In progress" {{ old('status', $project->status) == 'In progress' ? 'selected' : '' }}>Đang thực hiện</option>
                    <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="Suspended" {{ old('status', $project->status) == 'Suspended' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tiến độ -->
            <div class="form-group">
                <label for="progress">
                    <i class="fas fa-chart-pie"></i> Tiến độ (%)
                </label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input 
                        type="range" 
                        class="form-control" 
                        id="progress" 
                        name="progress" 
                        min="0" 
                        max="100" 
                        value="{{ old('progress', $project->progress ?? 0) }}"
                        style="flex: 1;">
                    <input 
                        type="number" 
                        class="form-control @error('progress') error @enderror" 
                        id="progressValue" 
                        min="0" 
                        max="100" 
                        value="{{ old('progress', $project->progress ?? 0) }}"
                        style="width: 70px; text-align: center;">
                    <span style="color: #6b7280;">%</span>
                </div>
                @error('progress')
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
                    rows="5" 
                    placeholder="Nhập mô tả chi tiết về dự án...">{{ old('description', $project->description) }}</textarea>
                @error('description')
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
                    value="{{ $project->created_at->format('d/m/Y H:i') }}" 
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
                    value="{{ $project->updated_at->format('d/m/Y H:i') }}" 
                    disabled>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
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
    // Sync range and number input for progress
    const progressRange = document.getElementById('progress');
    const progressValue = document.getElementById('progressValue');

    if (progressRange && progressValue) {
        progressRange.addEventListener('input', function() {
            progressValue.value = this.value;
        });

        progressValue.addEventListener('input', function() {
            if (this.value >= 0 && this.value <= 100) {
                progressRange.value = this.value;
            }
        });
    }

    console.log('✅ Project edit form loaded');
</script>
@endpush