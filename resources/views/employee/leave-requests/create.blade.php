@extends('layouts.employee')

@section('title', 'Tạo Đơn xin nghỉ')

@php
    $pageTitle = 'Tạo Đơn xin nghỉ';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.leave-requests.index') . '">Đơn xin nghỉ</a> / Tạo mới';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Tạo Đơn xin nghỉ</h1>
    <div class="page-actions">
        <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('employee.leave-requests.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Đơn xin nghỉ
        </h3>
        
        <div class="form-grid">
            <!-- Loại nghỉ -->
            <div class="form-group">
                <label for="leave_type_id">
                    <i class="fas fa-list"></i> Loại nghỉ 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('leave_type_id') error @enderror" id="leave_type_id" name="leave_type_id" required>
                    <option value="">-- Chọn loại nghỉ --</option>
                    @forelse($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->days_available ?? 0 }} ngày)
                        </option>
                    @empty
                        <option value="" disabled>Không có loại nghỉ</option>
                    @endforelse
                </select>
                @error('leave_type_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Từ ngày -->
            <div class="form-group">
                <label for="from_date">
                    <i class="fas fa-calendar-alt"></i> Từ ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('from_date') error @enderror" 
                    id="from_date" 
                    name="from_date" 
                    value="{{ old('from_date') }}"
                    onchange="calculateDays()"
                    required>
                @error('from_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Đến ngày -->
            <div class="form-group">
                <label for="to_date">
                    <i class="fas fa-calendar-alt"></i> Đến ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('to_date') error @enderror" 
                    id="to_date" 
                    name="to_date" 
                    value="{{ old('to_date') }}"
                    onchange="calculateDays()"
                    required>
                @error('to_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Số ngày -->
            <div class="form-group">
                <label for="number_of_days">
                    <i class="fas fa-calendar-check"></i> Số ngày nghỉ
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="number_of_days" 
                    name="number_of_days" 
                    value="{{ old('number_of_days', 0) }}"
                    readonly
                    style="background: #f9fafb; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính từ ngày từ và đến
                </small>
            </div>

            <!-- Lý do -->
            <div class="form-group full-width">
                <label for="reason">
                    <i class="fas fa-pencil-alt"></i> Lý do xin nghỉ 
                    <span class="required">*</span>
                </label>
                <textarea 
                    class="form-control @error('reason') error @enderror" 
                    id="reason" 
                    name="reason" 
                    rows="4"
                    placeholder="Nhập lý do xin nghỉ..."
                    required>{{ old('reason') }}</textarea>
                @error('reason')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Gửi đơn</span>
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function calculateDays() {
        const fromDate = new Date(document.getElementById('from_date').value);
        const toDate = new Date(document.getElementById('to_date').value);
        
        if (fromDate && toDate) {
            const days = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('number_of_days').value = days > 0 ? days : 0;
        }
    }
</script>
@endpush
