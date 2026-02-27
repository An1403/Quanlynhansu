@extends('layouts.employee')

@section('title', 'Chi tiết Đơn xin nghỉ')

@php
    $pageTitle = 'Chi tiết Đơn xin nghỉ';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.leave-requests.index') . '">Đơn xin nghỉ</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .page-header h1 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 28px;
            color: #111827;
            font-weight: 700;
        }

        .page-header h1 i {
            font-size: 32px;
            color: #667eea;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .card h3 {
            margin-bottom: 20px;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: #f9fafb;
            color: #111827;
        }

        .form-control[readonly] {
            cursor: not-allowed;
            color: #6b7280;
            background: #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
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

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-eye"></i> Chi tiết Đơn xin nghỉ
    </h1>
    <div class="page-actions">
        @if($leaveRequest->canEdit())
            <a href="{{ route('employee.leave-requests.edit', $leaveRequest->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                <span>Chỉnh sửa</span>
            </a>
        @endif
        <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<div class="card">
    <h3>
        <i class="fas fa-info-circle"></i> Thông tin Đơn xin nghỉ
    </h3>

    <div class="form-grid">
        <div class="form-group">
            <label><i class="fas fa-barcode"></i> Mã đơn</label>
            <input type="text" class="form-control" value="{{ $leaveRequest->request_id ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
            <label><i class="fas fa-list"></i> Loại nghỉ</label>
            <input type="text" class="form-control" value="{{ $leaveRequest->leaveType->name ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-alt"></i> Từ ngày</label>
            <input type="text" class="form-control" value="{{ $leaveRequest->formatted_start_date }}" readonly>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-alt"></i> Đến ngày</label>
            <input type="text" class="form-control" value="{{ $leaveRequest->formatted_end_date }}" readonly>
        </div>

        <div class="form-group">
            <label><i class="fas fa-info-circle"></i> Trạng thái</label>
            <div style="padding: 10px 12px;">
                @if($leaveRequest->status === 'pending')
                    <span class="badge badge-pending">
                        <i class="fas fa-hourglass-half"></i> Chờ duyệt
                    </span>
                @elseif($leaveRequest->status === 'approved')
                    <span class="badge badge-approved">
                        <i class="fas fa-check-circle"></i> Được phép
                    </span>
                @else
                    <span class="badge badge-rejected">
                        <i class="fas fa-times-circle"></i> Từ chối
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group full-width">
            <label><i class="fas fa-pencil-alt"></i> Lý do</label>
            <textarea class="form-control" rows="4" readonly>{{ $leaveRequest->reason ?? '-' }}</textarea>
        </div>

        @if($leaveRequest->rejected_reason)
            <div class="form-group full-width">
                <label><i class="fas fa-times-circle"></i> Lý do từ chối</label>
                <textarea class="form-control" rows="3" readonly>{{ $leaveRequest->rejected_reason }}</textarea>
            </div>
        @endif
    </div>
</div>

<!-- Nút hành động -->
<div class="action-buttons">
    @if($leaveRequest->canDelete())
        <form action="{{ route('employee.leave-requests.destroy', $leaveRequest->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                <span>Hủy đơn</span>
            </button>
        </form>
    @endif
    <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        <span>Quay lại</span>
    </a>
</div>

@endsection