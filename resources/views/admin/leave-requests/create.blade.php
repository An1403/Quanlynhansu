@extends('layouts.admin')

@section('title', 'Thêm Đơn xin nghỉ')

@php
    $pageTitle = 'Thêm Đơn xin nghỉ';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.leave-requests.index') . '">Đơn xin nghỉ</a> / Thêm mới';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Thêm Đơn xin nghỉ Mới</h1>
    <div class="page-actions">
        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.leave-requests.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Đơn xin nghỉ
        </h3>
        
        <div class="form-grid">
            <!-- Nhân viên -->
            <div class="form-group">
                <label for="user_id">
                    <i class="fas fa-user"></i> Nhân viên 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('user_id') error @enderror" id="user_id" name="user_id" required>
                    <option value="">-- Chọn nhân viên --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Từ ngày -->
            <div class="form-group">
                <label for="start_date">
                    <i class="fas fa-calendar-check"></i> Từ ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('start_date') error @enderror" 
                    id="start_date" 
                    name="start_date" 
                    value="{{ old('start_date') }}"
                    onchange="calculateDays()"
                    required>
                @error('start_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Đến ngày -->
            <div class="form-group">
                <label for="end_date">
                    <i class="fas fa-calendar-times"></i> Đến ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('end_date') error @enderror" 
                    id="end_date" 
                    name="end_date" 
                    value="{{ old('end_date') }}"
                    onchange="calculateDays()"
                    required>
                @error('end_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Số ngày -->
            <div class="form-group">
                <label for="days_count">
                    <i class="fas fa-calculator"></i> Số ngày xin nghỉ
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="days_count" 
                    name="days_count" 
                    value="0"
                    min="0"
                    readonly
                    style="background-color: #f3f4f6;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính từ ngày bắt đầu và ngày kết thúc
                </small>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-flag"></i> Trạng thái 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status" required>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Được phép</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Lý do -->
            <div class="form-group full-width">
                <label for="reason">
                    <i class="fas fa-align-left"></i> Lý do xin nghỉ 
                    <span class="required">*</span>
                </label>
                <textarea 
                    class="form-control @error('reason') error @enderror" 
                    id="reason" 
                    name="reason" 
                    rows="5" 
                    placeholder="Nhập lý do xin nghỉ..."
                    required>{{ old('reason') }}</textarea>
                <small style="color: #6b7280; font-size: 12px;">Tối đa 500 ký tự</small>
                @error('reason')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
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
    function calculateDays() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end >= start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('days_count').value = diffDays;
            } else {
                document.getElementById('days_count').value = 0;
                alert('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!');
            }
        }
    }

    console.log('✅ Leave request create form loaded');
</script>
@endpush