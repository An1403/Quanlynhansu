@extends('layouts.employee')

@section('title', 'Lịch sử Chấm công')

@php
    $pageTitle = 'Lịch sử Chấm công';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Chấm công';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .page-actions {
            display: flex;
            gap: 12px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.success { background: #10b981; }
        .stat-icon.info { background: #3b82f6; }
        .stat-icon.danger { background: #ef4444; }
        .stat-icon.warning { background: #f59e0b; }

        .stat-content h6 {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-content h4 {
            margin: 5px 0 0 0;
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .search-filter-bar {
            display: flex;
            gap: 12px;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: #9ca3af;
            font-size: 16px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-select {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .table thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .table tbody td {
            padding: 15px;
            color: #111827;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }

        .employee-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .employee-name {
            font-weight: 600;
            color: #111827;
        }

        .employee-code {
            font-size: 12px;
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-leave {
            background: #fef3c7;
            color: #b45309;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .btn-edit {
            background: #fef3c7;
            color: #b45309;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 20px;
            color: #374151;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .pagination {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-clock"></i>
        Lịch sử Chấm công
    </h1>
    <div class="page-actions">
        <a href="{{ route('employee.attendance.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Chấm công</span>
        </a>
    </div>
</div>

<!-- Thống kê -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h6>Ngày công</h6>
            <h4>{{ $statistics['worked_days'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <h6>Ngày nghỉ có phép</h6>
            <h4>{{ $statistics['leave_days'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <h6>Ngày vắng mặt</h6>
            <h4>{{ $statistics['absent_days'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-hourglass-end"></i>
        </div>
        <div class="stat-content">
            <h6>Đi muộn</h6>
            <h4>{{ $statistics['late_times'] ?? 0 }}</h4>
        </div>
    </div>
</div>

<!-- Bảng chấm công -->
<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="dateFilter" placeholder="Lọc theo ngày..." type="date">
        </div>
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="Present">Có mặt</option>
            <option value="Leave">Xin phép</option>
            <option value="Absent">Vắng mặt</option>
        </select>

        <select class="filter-select" id="monthFilter">
            <option value="">Tất cả tháng</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}">Tháng {{ $i }}</option>
            @endfor
        </select>

        <select class="filter-select" id="yearFilter">
            <option value="">Tất cả năm</option>
            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($attendances->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th style="text-align: center;">Số giờ làm</th>
                        <th>Dự án</th>
                        <th>Ghi chú</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $record)
                    <tr data-date="{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}" 
                        data-month="{{ \Carbon\Carbon::parse($record->date)->format('m') }}"
                        data-year="{{ \Carbon\Carbon::parse($record->date)->format('Y') }}"
                        data-status="{{ $record->status }}">
                        
                        <!-- Date -->
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</strong>
                        </td>
                        <!-- Check In -->
                        <td>
                            @if($record->check_in)
                                @if($record->check_in instanceof \Carbon\Carbon)
                                    {{ $record->check_in->format('H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($record->check_in)->format('H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        
                        <!-- Check Out -->
                        <td>
                            @if($record->check_out)
                                @if($record->check_out instanceof \Carbon\Carbon)
                                    {{ $record->check_out->format('H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($record->check_out)->format('H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        
                        <td style="text-align: center;">
                            <span style="font-weight: 600; color: #2c3e50;">{{ $record->working_hours ?? 0 }} h</span>
                        </td>
                        <td>{{ $record->project->name ?? '-' }}</td>
                        <td>{{ $record->notes ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($record->status === 'Present')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Có mặt</span>
                            @elseif($record->status === 'Leave')
                                <span class="badge badge-leave"><i class="fas fa-file-alt"></i> Xin phép</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Vắng mặt</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('employee.attendance.show', $record->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employee.attendance.edit', $record->id) }}" class="btn-icon btn-edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employee.attendance.destroy', $record->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                {{ $attendances->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-3x"></i>
                </div>
                <h3>Chưa có bản ghi chấm công</h3>
                <p>Hãy thêm bản ghi chấm công đầu tiên</p>
                <a href="{{ route('employee.attendance.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Chấm công</span>
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Filter by date
    document.getElementById('dateFilter').addEventListener('change', function() {
        filterTable();
    });

    // Filter by status
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
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
        const dateFilter = document.getElementById('dateFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const monthFilter = document.getElementById('monthFilter').value;
        const yearFilter = document.getElementById('yearFilter').value;
        
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const rowDate = row.getAttribute('data-date');
            const rowStatus = row.getAttribute('data-status');
            const rowMonth = row.getAttribute('data-month');
            const rowYear = row.getAttribute('data-year');
            
            let show = true;
            
            if (dateFilter && rowDate !== dateFilter) {
                show = false;
            }
            
            if (statusFilter && rowStatus !== statusFilter) {
                show = false;
            }
            
            if (monthFilter && rowMonth !== monthFilter) {
                show = false;
            }
            
            if (yearFilter && rowYear !== yearFilter) {
                show = false;
            }
            
            row.style.display = show ? '' : 'none';
        });
    }

    console.log('✅ Employee attendance index loaded');
</script>
@endpush