@extends('layouts.admin')

@section('title', 'Chi tiết Đơn xin phép')

@php
    $pageTitle = 'Chi tiết Đơn xin phép';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.leave-requests.index') . '">Đơn xin phép</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Chi tiết Đơn xin phép</h1>
    <div class="page-actions">
        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Leave Request Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Đơn xin phép
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-user"></i> Nhân viên
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ $leaveRequest->full_name }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-check"></i> Từ ngày
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d/m/Y') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-times"></i> Đến ngày
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d/m/Y') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calculator"></i> Số ngày xin phép
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 8px; background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 4px; font-weight: 600;">
                    <i class="fas fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($leaveRequest->end_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->start_date)) + 1 }} ngày
                </span>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-flag"></i> Trạng thái
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px;">
                @if($leaveRequest->status === 'pending')
                    <span class="badge" style="background: #fef3c7; color: #b45309;">
                        <i class="fas fa-hourglass-half"></i> Chờ duyệt
                    </span>
                @elseif($leaveRequest->status === 'approved')
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Đã duyệt
                    </span>
                @else
                    <span class="badge badge-danger">
                        <i class="fas fa-times-circle"></i> Đã từ chối
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group full-width">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-align-left"></i> Lý do xin phép
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                {{ $leaveRequest->reason }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày gửi
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
@if($leaveRequest->status === 'pending')
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-check-double"></i> Xử lý Đơn xin phép
    </h3>

    <div style="display: flex; gap: 12px;">
        <form action="{{ route('admin.leave-requests.approve', $leaveRequest->id) }}" method="POST" style="flex: 1;">
            @csrf
            <button type="submit" class="btn btn-success" style="width: 100%;">
                <i class="fas fa-check-circle"></i> Phê duyệt đơn
            </button>
        </form>

        <form action="{{ route('admin.leave-requests.reject', $leaveRequest->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Bạn chắc chắn muốn từ chối đơn xin phép này?')">
            @csrf
            <button type="submit" class="btn btn-delete" style="width: 100%; background-color: #ef4444; color: white;">
                <i class="fas fa-times-circle"></i> Từ chối đơn
            </button>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    console.log('✅ Leave request show page loaded');
</script>
@endpush