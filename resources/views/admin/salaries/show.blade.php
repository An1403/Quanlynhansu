@extends('layouts.admin')

@section('title', 'Chi tiết Lương')

@php
    $pageTitle = 'Chi tiết Lương';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.salaries.index') . '">Lương</a> / Chi tiết';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-money-bill-wave"></i> Chi tiết Lương</h1>
    <div class="page-actions">
        <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Salary Information -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-info-circle"></i> Thông tin Lương
    </h3>
    
    <div class="form-grid">
        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-user"></i> Nhân viên
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 500;">
                {{ $salary->full_name }} ({{ $salary->employee_code }})
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Kỳ lương
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827; font-weight: 600;">
                Tháng {{ str_pad($salary->month, 2, '0', STR_PAD_LEFT) }}/{{ $salary->year }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-hourglass-half"></i> Tổng giờ làm
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ $salary->total_hours }} giờ
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-money-bill"></i> Lương cơ bản
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ number_format($salary->base_salary, 0, ',', '.') }} đ
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-coins"></i> Phụ cấp
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ number_format($salary->allowance, 0, ',', '.') }} đ
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-gift"></i> Thưởng
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ number_format($salary->bonus, 0, ',', '.') }} đ
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-minus-circle"></i> Khấu trừ
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ number_format($salary->deduction, 0, ',', '.') }} đ
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-check-circle"></i> Lương thực
            </label>
            <div style="padding: 12px 15px; background-color: #dcfce7; border-radius: 6px; color: #166534;">
                <span style="font-size: 18px; font-weight: 700;">
                    {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                </span>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-calendar-alt"></i> Ngày tạo
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($salary->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="form-group">
            <label style="color: #6b7280; font-weight: 600; font-size: 14px;">
                <i class="fas fa-clock"></i> Cập nhật lần cuối
            </label>
            <div style="padding: 12px 15px; background-color: #f3f4f6; border-radius: 6px; color: #111827;">
                {{ \Carbon\Carbon::parse($salary->updated_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Salary Calculation Details -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <i class="fas fa-calculator"></i> Chi tiết Tính lương
    </h3>
    
    <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 10px; text-align: right; font-weight: 600;">Lương cơ bản:</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($salary->base_salary, 0, ',', '.') }} đ</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 10px; text-align: right; font-weight: 600;">+ Phụ cấp:</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($salary->allowance, 0, ',', '.') }} đ</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 10px; text-align: right; font-weight: 600;">+ Thưởng:</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($salary->bonus, 0, ',', '.') }} đ</td>
            </tr>
            <tr style="border-bottom: 2px solid #111827;">
                <td style="padding: 10px; text-align: right; font-weight: 600;">- Khấu trừ:</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($salary->deduction, 0, ',', '.') }} đ</td>
            </tr>
            <tr style="background-color: #dcfce7;">
                <td style="padding: 12px; text-align: right; font-weight: 700; color: #166534;">Tổng Lương:</td>
                <td style="padding: 12px; text-align: right; font-size: 18px; font-weight: 700; color: #166534;">
                    {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                </td>
            </tr>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Salary show page loaded');
</script>
@endpush