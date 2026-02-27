@extends('layouts.admin')

@section('title', 'Quản lý Lương')

@php
    $pageTitle = 'Quản lý Lương';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.salaries.index') . '">Lương</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-money-bill-wave"></i> Quản lý Lương</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-success" onclick="openExportModal()">
            <i class="fas fa-file-excel"></i>
            <span>Xuất Excel</span>
        </button>
        
        <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Lương</span>
        </a>
    </div>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên nhân viên...">
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

    <!-- Table -->
    <div class="table-container">
        @if($salaries->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
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
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ strtoupper(substr($salary->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="employee-name">{{ $salary->full_name }}</div>
                                    <div class="employee-code">{{ $salary->employee_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: 600;">{{ str_pad($salary->month, 2, '0', STR_PAD_LEFT) }}/{{ $salary->year }}</span>
                        </td>
                        <td style="text-align: right;">{{ number_format($salary->base_salary, 0, ',', '.') }} đ</td>
                        <td style="text-align: right;">{{ number_format($salary->allowance, 0, ',', '.') }} đ</td>
                        <td style="text-align: right;">{{ number_format($salary->bonus, 0, ',', '.') }} đ</td>
                        <td style="text-align: right;">{{ number_format($salary->deduction, 0, ',', '.') }} đ</td>
                        <td style="text-align: right;">
                            <span style="font-weight: 700; color: #059669; font-size: 15px;">
                                {{ number_format($salary->total_salary, 0, ',', '.') }} đ
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi lương này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-delete" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $salaries->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có bản ghi lương</h3>
                <p>Hãy thêm bản ghi lương đầu tiên</p>
                <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Lương</span>
                </a>
            </div>
        @endif
    </div>
</div>

<div id="exportModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="fas fa-file-excel"></i> Xuất File Excel</h3>
            <button type="button" class="close-modal" onclick="closeExportModal()">&times;</button>
        </div>
        
        <form action="{{ route('admin.salaries.export') }}" method="GET">
            <div class="modal-body">
                <div class="form-group">
                    <label for="export_month">
                        <i class="fas fa-calendar-alt"></i> Tháng
                        <span class="required">*</span>
                    </label>
                    <select class="form-control" id="export_month" name="month" required>
                        <option value="">-- Chọn tháng --</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                Tháng {{ $m }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="export_year">
                        <i class="fas fa-calendar"></i> Năm
                        <span class="required">*</span>
                    </label>
                    <select class="form-control" id="export_year" name="year" required>
                        <option value="">-- Chọn năm --</option>
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div style="background: #f0fdf4; border-left: 4px solid #10b981; padding: 12px; margin-top: 15px; border-radius: 4px;">
                    <p style="margin: 0; color: #065f46; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> 
                        File Excel sẽ chứa tất cả bản ghi lương của tháng được chọn
                    </p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeExportModal()">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download"></i> Tải xuống
                </button>
            </div>
        </form>
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
            const employeeName = row.querySelector('.employee-name').textContent.toLowerCase();
            row.style.display = employeeName.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter by month
    document.getElementById('monthFilter').addEventListener('change', function() {
        const filterMonth = this.value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const month = row.getAttribute('data-month');
            row.style.display = (filterMonth === '' || month === filterMonth) ? '' : 'none';
        });
    });

    // Filter by year
    document.getElementById('yearFilter').addEventListener('change', function() {
        const filterYear = this.value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const year = row.getAttribute('data-year');
            row.style.display = (filterYear === '' || year === filterYear) ? '' : 'none';
        });
    });

    console.log('✅ Salaries index loaded');

    // Modal functions
    function openExportModal() {
        document.getElementById('exportModal').style.display = 'flex';
    }
    
    function closeExportModal() {
        document.getElementById('exportModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('exportModal');
        if (event.target === modal) {
            closeExportModal();
        }
    }
    
    console.log('✅ Export modal loaded');
</script>
@endpush

@push('styles')
<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 500px;
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .modal-header h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 600;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        color: #6b7280;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }
    
    .close-modal:hover {
        background-color: #f3f4f6;
        color: #111827;
    }
    
    .modal-body {
        padding: 24px;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
</style>
@endpush