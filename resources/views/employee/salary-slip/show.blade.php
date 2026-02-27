@extends('layouts.employee')

@section('title', 'Chi Tiết Lương')

@php
    $pageTitle = 'Chi Tiết Lương';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.salary-slip.index') . '">Lương</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-file-invoice-dollar"></i> 
        Chi Tiết Lương Tháng {{ str_pad($salary->month, 2, '0', STR_PAD_LEFT) }}/{{ $salary->year }}
    </h1>
    <div class="page-actions">
        <a href="{{ route('employee.salary-slip.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
        
    </div>
</div>

<div class="salary-detail-container">
    {{-- Thông tin nhân viên --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user"></i> Thông Tin Nhân Viên</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-id-badge"></i> Mã nhân viên:</label>
                    <span>{{ $salary->employee->employee_code }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-user"></i> Họ và tên:</label>
                    <span>{{ $salary->employee->full_name }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-building"></i> Phòng ban:</label>
                    <span>{{ $salary->employee->department->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-briefcase"></i> Chức vụ:</label>
                    <span>{{ $salary->employee->position->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chi tiết lương --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calculator"></i> Chi Tiết Lương</h3>
        </div>
        <div class="card-body">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Hạng mục</th>
                        <th style="text-align: right;">Số tiền</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Thu nhập --}}
                    <tr class="section-header">
                        <td colspan="2"><strong>THU NHẬP</strong></td>
                    </tr>
                    <tr>
                        <td>
                            <i class="fas fa-money-bill-wave"></i> Lương cơ bản
                            <small>({{ $workingDaysInMonth }} ngày × {{ number_format($dailySalary, 0, ',', '.') }} đ)</small>
                        </td>
                        <td style="text-align: right; color: #059669; font-weight: 600;">
                            {{ number_format($salary->base_salary, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="fas fa-hand-holding-usd"></i> Phụ cấp
                            @if($salary->employee->position)
                                <small>({{ $salary->employee->position->name }})</small>
                            @endif
                        </td>
                        <td style="text-align: right; color: #059669; font-weight: 600;">
                            {{ number_format($salary->allowance, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-gift"></i> Thưởng</td>
                        <td style="text-align: right; color: #059669; font-weight: 600;">
                            {{ number_format($salary->bonus, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    <tr class="subtotal-row">
                        <td><strong>Tổng thu nhập</strong></td>
                        <td style="text-align: right;">
                            <strong style="color: #059669;">
                                {{ number_format($salary->base_salary + $salary->allowance + $salary->bonus, 0, ',', '.') }} đ
                            </strong>
                        </td>
                    </tr>

                    {{-- Khấu trừ --}}
                    <tr class="section-header">
                        <td colspan="2"><strong>KHẤU TRỪ</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-minus-circle"></i> Tổng khấu trừ</td>
                        <td style="text-align: right; color: #dc2626; font-weight: 600;">
                            {{ number_format($salary->deduction, 0, ',', '.') }} đ
                        </td>
                    </tr>

                    {{-- Thực lĩnh --}}
                    <tr class="total-row">
                        <td><strong><i class="fas fa-wallet"></i> LƯƠNG THỰC LĨNH</strong></td>
                        <td style="text-align: right;">
                            <strong style="color: #059669; font-size: 20px;">
                                {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Thông tin bổ sung --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Thông Tin Bổ Sung</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-clock"></i> Tổng giờ làm việc:</label>
                    <span>{{ number_format($salary->total_hours, 2) }} giờ</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-calculator"></i> Lương theo ngày:</label>
                    <span>{{ number_format($dailySalary, 0, ',', '.') }} đ/ngày</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-hourglass-half"></i> Lương theo giờ:</label>
                    <span>{{ number_format($hourlyRate, 0, ',', '.') }} đ/giờ</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-calendar-check"></i> Ngày tạo:</label>
                    <span>{{ $salary->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .salary-detail-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    }

    .card-header h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 600;
    }

    .card-header h3 i {
        color: #667eea;
        margin-right: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-item label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .info-item label i {
        margin-right: 6px;
        color: #9ca3af;
    }

    .info-item span {
        font-size: 15px;
        color: #111827;
        font-weight: 600;
    }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table thead th {
        background: #f9fafb;
        padding: 12px 16px;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }

    .detail-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-table tbody td small {
        display: block;
        color: #6b7280;
        font-size: 13px;
        margin-top: 4px;
    }

    .section-header td {
        background: #f9fafb;
        padding: 12px 16px !important;
        font-weight: 600;
        color: #374151;
    }

    .subtotal-row {
        background: #f0fdf4 !important;
    }

    .subtotal-row td {
        padding: 14px 16px !important;
        border-top: 2px solid #d1fae5 !important;
        border-bottom: 2px solid #d1fae5 !important;
    }

    .total-row {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%) !important;
    }

    .total-row td {
        padding: 18px 16px !important;
        border-top: 3px solid #10b981 !important;
        border-bottom: 3px solid #10b981 !important;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush