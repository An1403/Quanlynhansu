@extends('layouts.employee')

@section('title', 'Chỉnh sửa Đơn xin nghỉ')

@php
    $pageTitle = 'Chỉnh sửa Đơn xin nghỉ';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.leave-requests.index') . '">Đơn xin nghỉ</a> / Chỉnh sửa';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit"></i> Chỉnh sửa Đơn xin nghỉ</h1>
    <div class="page-actions">
        <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('employee.leave-requests.update', $leaveRequest->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Đơn xin nghỉ
        </h3>
        
        <div class="form-grid">
            <!-- Mã đơn (chỉ đọc) -->
            <div class="form-group">
                <label><i class="fas fa-barcode"></i> Mã đơn</label>
                <input type="text" class="form-control" value="{{ $leaveRequest->request_id?? 'N/A' }}" readonly style="background: #f9fafb;">
            </div>

            <!-- Loại nghỉ -->
            <div class="form-group">
                <label for="leave_type_id">
                    <i class="fas fa-list"></i> Loại nghỉ 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('leave_type_id') error @enderror" id="leave_type_id" name="leave_type_id" required>
                    <option value="">-- Chọn loại nghỉ --</option>
                    @forelse($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ old('leave_type_id', $leaveRequest->types_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
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
                    value="{{ old('from_date', $leaveRequest->start_date?->format('Y-m-d') ?? $leaveRequest->from_date?->format('Y-m-d')) }}"
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
                    value="{{ old('to_date', $leaveRequest->end_date?->format('Y-m-d') ?? $leaveRequest->to_date?->format('Y-m-d')) }}"
                    onchange="calculateDays()"
                    required>
                @error('to_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            

            <!-- Lý do -->
            <div class="form-group full-width">
                <label for="reason">
                    <i class="fas fa-pencil-alt"></i> Lý do 
                    <span class="required">*</span>
                </label>
                <textarea 
                    class="form-control @error('reason') error @enderror" 
                    id="reason" 
                    name="reason" 
                    rows="4"
                    placeholder="Nhập lý do xin nghỉ..."
                    required>{{ old('reason', $leaveRequest->reason ?? '') }}</textarea>
                @error('reason')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Lý do từ chối (chỉ đọc, nếu có) -->
            @if($leaveRequest->rejected_reason)
                <div class="form-group full-width">
                    <label><i class="fas fa-times-circle"></i> Lý do từ chối</label>
                    <textarea class="form-control" rows="3" readonly style="background: #f9fafb;">{{ $leaveRequest->rejected_reason }}</textarea>
                </div>
            @endif
        </div>
    </div>

    <!-- Nút hành động -->
    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy</span>
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <span>Lưu thay đổi</span>
        </button>
    </div>
</form>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
        font-size: 14px;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control[readonly] {
        cursor: not-allowed;
        color: #6b7280;
    }

    .form-control.error {
        border-color: #dc2626;
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

    .card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }
</style>

<script>
    console.log('✅ Edit leave request form loaded');
</script>

@endsection