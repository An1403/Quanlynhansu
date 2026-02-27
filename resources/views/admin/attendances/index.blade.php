@extends('layouts.admin')

@section('title', 'Quản lý Chấm công')

@php
    $pageTitle = 'Quản lý Chấm công';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.attendances.index') . '">Chấm công</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clock"></i> Quản lý Chấm công</h1>
    <div class="page-actions">
        <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Chấm công</span>
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
        
        <input type="date" id="dateFilter" class="filter-select" placeholder="Lọc theo ngày">
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="Present">Có mặt</option>
            <option value="Leave">Xin phép</option>
            <option value="Absent">Vắng mặt</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($attendances->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Ngày</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th style="text-align: center;">Số giờ làm</th>
                        <th>Dự án</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr data-date="{{ $attendance->date }}" data-status="{{ $attendance->status }}">
                        <td>
                            <div class="employee-info">
                                @if($attendance->photo)
                                    <img src="{{ asset('storage/' . $attendance->photo) }}" alt="{{ $attendance->full_name }}" class="employee-avatar">
                                @else
                                    <div class="employee-placeholder">
                                        {{ strtoupper(substr($attendance->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="employee-name">{{ $attendance->full_name }}</div>
                                    <div class="employee-code">{{ $attendance->employee_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ date('d/m/Y', strtotime($attendance->date)) }}</td>
                        <td>{{ $attendance->check_in ? substr($attendance->check_in, 0, 5) : '-' }}</td>
                        <td>{{ $attendance->check_in ? substr($attendance->check_out, 0, 5) : '-' }}</td>
                        <td style="text-align: center;">
                            <span style="font-weight: 600; color: #2c3e50;">
                                {{ number_format($attendance->working_hours ?? 0, 1) }} h
                            </span>
                        </td>
                        <td>{{ $attendance->project_name ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($attendance->status === 'Present')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Có mặt</span>
                            @elseif($attendance->status === 'Leave')
                                <span class="badge" style="background: #fef3c7; color: #b45309;">
                                    <i class="fas fa-file-alt"></i> Xin phép
                                </span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Vắng mặt</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.attendances.show', $attendance->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi chấm công này?')">
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
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có bản ghi chấm công</h3>
                <p>Hãy thêm bản ghi chấm công đầu tiên</p>
                <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary">
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
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const employeeName = row.querySelector('.employee-name').textContent.toLowerCase();
            row.style.display = employeeName.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter by date
    document.getElementById('dateFilter').addEventListener('change', function() {
        const filterDate = this.value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const rowDate = row.getAttribute('data-date');
            row.style.display = (filterDate === '' || rowDate === filterDate) ? '' : 'none';
        });
    });

    // Filter by status
    document.getElementById('statusFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            row.style.display = (filterValue === '' || status === filterValue) ? '' : 'none';
        });
    });

    console.log('✅ Attendances index loaded');
</script>
@endpush