@extends('layouts.accountant')

@section('title', 'Dashboard Kế Toán')

@php
    $pageTitle = 'Dashboard Kế Toán';
    $breadcrumb = '<a href="' . route('accountant.dashboard') . '">Home</a> / Dashboard';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <style>
        /* Salary Chart Styles */
        .salary-chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 15px;
            padding: 30px 20px 20px 20px;
            min-height: 300px;
            background: linear-gradient(180deg, #f9fafb 0%, #ffffff 100%);
            border-radius: 8px;
        }

        .salary-bar {
            width: 70px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 8px 8px 0 0;
            height: var(--bar-height, 50px);
            min-height: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
            position: relative;
            cursor: pointer;
        }

        .salary-bar:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(16, 185, 129, 0.3);
        }

        .salary-bar-label {
            margin-top: 10px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            line-height: 1.4;
        }

        .salary-bar-label strong {
            font-size: 13px;
            display: block;
            margin-top: 3px;
            color: #059669;
        }

        /* Financial Summary Cards */
        .finance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .finance-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .finance-card.income {
            border-color: #10b981;
        }

        .finance-card.expense {
            border-color: #ef4444;
        }

        .finance-card h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6b7280;
        }

        .finance-card .amount {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .finance-card.income .amount {
            color: #10b981;
        }

        .finance-card.expense .amount {
            color: #ef4444;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: #6b7280;
            font-size: 18px;
            margin: 10px 0;
        }
    </style>
@endpush

@section('content')
<!-- Stats Cards - Row 1 -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_salary_this_month'], 0, ',', '.') }} đ</div>
        <div class="stat-label">Tổng Lương Tháng Này</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-value">{{ $stats['employees_with_salary'] }}</div>
        <div class="stat-label">Nhân Viên Có Lương</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div class="stat-value">{{ $stats['salary_records_this_month'] }}</div>
        <div class="stat-label">Bảng Lương Tháng Này</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_working_hours'], 1) }}h</div>
        <div class="stat-label">Tổng Giờ Làm Việc</div>
    </div>
</div>

<!-- Financial Summary -->
<div class="finance-summary">
    <div class="finance-card income">
        <h4><i class="fa-solid fa-arrow-trend-up"></i> Thu Nhập Tháng Này</h4>
        <p class="amount">{{ number_format($financial_summary['total_income'], 0, ',', '.') }} đ</p>
        <small style="color: #6b7280;">Lương CB + Phụ cấp + Thưởng</small>
    </div>

    <div class="finance-card expense">
        <h4><i class="fa-solid fa-arrow-trend-down"></i> Khấu Trừ Tháng Này</h4>
        <p class="amount">{{ number_format($financial_summary['total_deduction'], 0, ',', '.') }} đ</p>
        <small style="color: #6b7280;">Tổng các khoản khấu trừ</small>
    </div>

    <div class="finance-card" style="border-color: #6366f1;">
        <h4><i class="fa-solid fa-wallet"></i> Thực Chi Tháng Này</h4>
        <p class="amount" style="color: #6366f1;">{{ number_format($financial_summary['net_salary'], 0, ',', '.') }} đ</p>
        <small style="color: #6b7280;">Số tiền thực tế phải chi trả</small>
    </div>

    <div class="finance-card" style="border-color: #14b8a6;">
        <h4><i class="fa-solid fa-chart-line"></i> TB Lương/Người</h4>
        <p class="amount" style="color: #14b8a6;">{{ number_format($financial_summary['avg_salary'], 0, ',', '.') }} đ</p>
        <small style="color: #6b7280;">Lương trung bình mỗi nhân viên</small>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Recent Salaries -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Bảng Lương Mới Nhất</h3>
            <a href="{{ route('accountant.salaries.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($recent_salaries->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân Viên</th>
                        <th>Tháng/Năm</th>
                        <th style="text-align: right;">Lương Thực</th>
                        <th style="text-align: center;">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_salaries as $salary)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                                    {{ strtoupper(substr($salary->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #111827;">{{ $salary->full_name }}</div>
                                    <small style="color: #9ca3af;">{{ $salary->employee_code }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600;">{{ str_pad($salary->month, 2, '0', STR_PAD_LEFT) }}/{{ $salary->year }}</td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">
                            {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-success"><i class="fa-solid fa-check-circle"></i> Đã tính</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <h3>Chưa có bảng lương</h3>
                <p>Chưa có bản ghi lương nào trong hệ thống</p>
            </div>
        @endif
    </div>

    <!-- Attendance Summary -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-user-clock"></i> Chấm Công Hôm Nay</h3>
            <a href="{{ route('accountant.attendances.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($today_attendance->count() > 0)
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div style="text-align: center; padding: 15px; background: #dcfce7; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #059669;">{{ $attendance_stats['present'] }}</div>
                        <div style="font-size: 13px; color: #065f46; margin-top: 5px;">Có mặt</div>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #fed7aa; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #c2410c;">{{ $attendance_stats['leave'] }}</div>
                        <div style="font-size: 13px; color: #7c2d12; margin-top: 5px;">Nghỉ phép</div>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #fecaca; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #b91c1c;">{{ $attendance_stats['absent'] }}</div>
                        <div style="font-size: 13px; color: #7f1d1d; margin-top: 5px;">Vắng mặt</div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-calendar-xmark"></i>
                <p>Chưa có dữ liệu chấm công hôm nay</p>
            </div>
        @endif
    </div>
</div>

<!-- Salary Chart (6 Months) -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-chart-bar"></i> Thống Kê Lương 6 Tháng Gần Nhất</h3>
    </div>
    
    @if($salary_chart->count() > 0)
        <div class="salary-chart-container">
            @foreach($salary_chart as $data)
                @php
                    $maxSalary = $salary_chart->max('total_salary') ?: 1;
                    $barHeight = $data->total_salary > 0 ? ($data->total_salary / $maxSalary) * 250 : 5;
                @endphp
                <div style="text-align: center;">
                    <div class="salary-bar" 
                         style="--bar-height: {{ $barHeight }}px;"
                         title="Tháng {{ $data->month }}/{{ $data->year }}: {{ number_format($data->total_salary, 0, ',', '.') }} đ">
                    </div>
                    <div class="salary-bar-label">
                        Tháng {{ $data->month }}<br>
                        <small style="color: #9ca3af; font-size: 11px;">{{ $data->year }}</small><br>
                        <strong>{{ number_format($data->total_salary / 1000000, 1) }}M</strong>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Legend -->
        <div style="padding: 15px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 20px; font-size: 13px; color: #6b7280;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 3px;"></div>
                    <span>Tổng lương</span>
                </div>
            </div>
            <div style="font-size: 13px; color: #9ca3af;">
                <i class="fa-solid fa-calendar-days"></i> Đơn vị: <strong style="color: #111827;">Triệu đồng</strong>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-chart-simple"></i>
            <h3>Chưa có dữ liệu</h3>
            <p>Chưa có dữ liệu lương trong 6 tháng gần nhất</p>
        </div>
    @endif
</div>

<!-- Department Salary Breakdown -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-building"></i> Tổng Lương Theo Phòng Ban (Tháng Này)</h3>
    </div>
    
    @if($salary_by_department && count($salary_by_department) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Phòng Ban</th>
                    <th style="text-align: center;">Số Nhân Viên</th>
                    <th style="text-align: right;">Tổng Lương CB</th>
                    <th style="text-align: right;">Phụ Cấp</th>
                    <th style="text-align: right;">Thưởng</th>
                    <th style="text-align: right;">Tổng Thực Chi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salary_by_department as $dept)
                <tr>
                    <td><strong>{{ $dept->department_name ?? 'Chưa phân phòng' }}</strong></td>
                    <td style="text-align: center;">
                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                            {{ $dept->employee_count }}
                        </span>
                    </td>
                    <td style="text-align: right; color: #374151;">{{ number_format($dept->total_base_salary, 0, ',', '.') }} đ</td>
                    <td style="text-align: right; color: #059669;">{{ number_format($dept->total_allowance, 0, ',', '.') }} đ</td>
                    <td style="text-align: right; color: #0891b2;">{{ number_format($dept->total_bonus, 0, ',', '.') }} đ</td>
                    <td style="text-align: right; color: #059669; font-weight: 700; font-size: 15px;">
                        {{ number_format($dept->total_salary, 0, ',', '.') }} đ
                    </td>
                </tr>
                @endforeach
                <tr style="background: #f9fafb; font-weight: 700; border-top: 2px solid #e5e7eb;">
                    <td colspan="2"><strong>TỔNG CỘNG</strong></td>
                    <td style="text-align: right;">{{ number_format($salary_by_department->sum('total_base_salary'), 0, ',', '.') }} đ</td>
                    <td style="text-align: right;">{{ number_format($salary_by_department->sum('total_allowance'), 0, ',', '.') }} đ</td>
                    <td style="text-align: right;">{{ number_format($salary_by_department->sum('total_bonus'), 0, ',', '.') }} đ</td>
                    <td style="text-align: right; color: #059669; font-size: 16px;">
                        {{ number_format($salary_by_department->sum('total_salary'), 0, ',', '.') }} đ
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-inbox"></i>
            <h3>Chưa có dữ liệu</h3>
            <p>Chưa có dữ liệu lương theo phòng ban</p>
        </div>
    @endif
</div>

<!-- Pending Tasks / Notifications -->
<div class="content-grid" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-tasks"></i> Công Việc Cần Làm</h3>
        </div>
        
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div>
                        <strong style="color: #92400e;">Tính lương tháng {{ date('m/Y') }}</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #78350f;">
                            {{ $stats['employees_without_salary'] }} nhân viên chưa có bảng lương
                        </p>
                    </div>
                    <a href="{{ route('accountant.salaries.create') }}" class="btn btn-warning" style="font-size: 13px;">
                        <i class="fa-solid fa-plus"></i> Tạo lương
                    </a>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #dbeafe; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <div>
                        <strong style="color: #1e3a8a;">Kiểm tra chấm công</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #1e40af;">
                            Rà soát dữ liệu chấm công trước khi tính lương
                        </p>
                    </div>
                    <a href="{{ route('accountant.attendances.index') }}" class="btn btn-primary" style="font-size: 13px;">
                        <i class="fa-solid fa-clipboard-check"></i> Xem
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-bell"></i> Thông Báo</h3>
        </div>
        
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @if($stats['salary_records_this_month'] > 0)
                    <div style="padding: 12px; background: #dcfce7; border-radius: 8px; border-left: 4px solid #10b981;">
                        <strong style="color: #065f46;">✓ Đã tạo {{ $stats['salary_records_this_month'] }} bảng lương tháng này</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #047857;">
                            Tổng chi: {{ number_format($stats['total_salary_this_month'], 0, ',', '.') }} đ
                        </p>
                    </div>
                @endif

                <div style="padding: 12px; background: #e0e7ff; border-radius: 8px; border-left: 4px solid #6366f1;">
                    <strong style="color: #3730a3;">Trung bình giờ làm việc</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #4338ca;">
                        {{ number_format($stats['avg_working_hours'], 1) }} giờ/nhân viên
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/dashboard.js') }}"></script>
@endpush