@extends('layouts.employee')

@section('title', 'Bảng Lương Của Tôi')

@php
    $pageTitle = 'Bảng Lương Của Tôi';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.salary-slip.index') . '">Lương</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-money-bill-wave"></i> Bảng Lương Của Tôi</h1>
</div>

{{-- Thông tin tổng quan --}}
<div class="salary-overview">
    <div class="overview-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-coins"></i>
        </div>
        <div class="card-info">
            <h3>Lương Tháng Này</h3>
            <p class="amount">
                @if($currentMonthSalary)
                    {{ number_format($currentMonthSalary->total_salary, 0, ',', '.') }} đ
                @else
                    <span style="color: #9ca3af;">Chưa có dữ liệu</span>
                @endif
            </p>
        </div>
    </div>

    <div class="overview-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="card-info">
            <h3>Tổng Thu Nhập</h3>
            <p class="amount">{{ number_format($totalIncome, 0, ',', '.') }} đ</p>
        </div>
    </div>

    <div class="overview-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-info">
            <h3>Số Tháng Có Lương</h3>
            <p class="amount">{{ $salaries->total() }} tháng</p>
        </div>
    </div>
</div>

<div class="card">
    {{-- Search & Filter --}}
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tháng/năm...">
        </div>
        
        <select class="filter-select" id="monthFilter">
            <option value="">Tất cả tháng</option>
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">Tháng {{ $m }}</option>
            @endfor
        </select>

        <select class="filter-select" id="yearFilter">
            <option value="">Tất cả năm</option>
            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    {{-- Table --}}
    <div class="table-container">
        @if($salaries->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th style="text-align: center;">Tháng/Năm</th>
                        <th style="text-align: right;">Lương cơ bản</th>
                        <th style="text-align: right;">Phụ cấp</th>
                        <th style="text-align: right;">Thưởng</th>
                        <th style="text-align: right;">Khấu trừ</th>
                        <th style="text-align: right;">Lương thực</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr data-month="{{ $salary->month }}" data-year="{{ $salary->year }}">
                        <td style="text-align: center;">
                            <span style="font-weight: 600; font-size: 15px;">
                                <i class="fas fa-calendar-alt" style="color: #6b7280; margin-right: 5px;"></i>
                                {{ str_pad($salary->month, 2, '0', STR_PAD_LEFT) }}/{{ $salary->year }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span style="color: #374151;">{{ number_format($salary->base_salary, 0, ',', '.') }} đ</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="color: #059669;">{{ number_format($salary->allowance, 0, ',', '.') }} đ</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="color: #0891b2;">{{ number_format($salary->bonus, 0, ',', '.') }} đ</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="color: #dc2626;">{{ number_format($salary->deduction, 0, ',', '.') }} đ</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="font-weight: 700; color: #059669; font-size: 16px;">
                                {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('employee.salary-slip.show', $salary->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="pagination">
                {{ $salaries->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có bản ghi lương</h3>
                <p>Bạn chưa có bản ghi lương nào. Vui lòng liên hệ phòng nhân sự để biết thêm chi tiết.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const monthYear = row.querySelector('td:first-child').textContent.toLowerCase();
            row.style.display = monthYear.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter by month
    document.getElementById('monthFilter').addEventListener('change', function() {
        filterTable();
    });

    // Filter by year
    document.getElementById('yearFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const filterMonth = document.getElementById('monthFilter').value;
        const filterYear = document.getElementById('yearFilter').value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const month = row.getAttribute('data-month');
            const year = row.getAttribute('data-year');
            
            const monthMatch = filterMonth === '' || month === filterMonth;
            const yearMatch = filterYear === '' || year === filterYear;
            
            row.style.display = (monthMatch && yearMatch) ? '' : 'none';
        });
    }

    // Print salary slip
    function printSalary(salaryId) {
        window.open('/employee/salaries/' + salaryId + '/print', '_blank');
    }

    console.log('✅ Employee salaries index loaded');
</script>
@endpush

@push('styles')
<style>
    /* Salary Overview Cards */
    .salary-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .overview-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .overview-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    
    .card-info h3 {
        margin: 0 0 8px 0;
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }
    
    .card-info .amount {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }
    
    /* Print Button Style */
    .btn-print {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .btn-print:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .salary-overview {
            grid-template-columns: 1fr;
        }
        
        .card-info .amount {
            font-size: 20px;
        }
    }
</style>
@endpush