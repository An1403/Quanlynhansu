@extends('layouts.admin')

@section('title', 'Quản lý Đơn xin phép')

@php
    $pageTitle = 'Quản lý Đơn xin phép';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.leave-requests.index') . '">Đơn xin phép</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Quản lý Đơn xin phép</h1>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên nhân viên...">
        </div>
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="pending">Chờ duyệt</option>
            <option value="approved">Đã duyệt</option>
            <option value="rejected">Đã từ chối</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($leaveRequests->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th style="text-align: center;">Từ ngày</th>
                        <th style="text-align: center;">Đến ngày</th>
                        <th style="text-align: center;">Số ngày</th>
                        <th>Lý do</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveRequests as $leave)
                    <tr data-status="{{ $leave->status }}">
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ strtoupper(substr($leave->full_name, 0, 1)) }}
                                </div>
                                <div class="employee-name">{{ $leave->full_name }}</div>
                            </div>
                        </td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">
                            <span style="font-weight: 600; color: #2c3e50;">
                                {{ \Carbon\Carbon::parse($leave->end_date)->diffInDays(\Carbon\Carbon::parse($leave->start_date)) + 1 }} ngày
                            </span>
                        </td>
                        <td>
                            <span title="{{ $leave->reason }}">
                                {{ strlen($leave->reason) > 30 ? substr($leave->reason, 0, 30) . '...' : $leave->reason }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($leave->status === 'pending')
                                <span class="badge" style="background: #fef3c7; color: #b45309;">
                                    <i class="fas fa-hourglass-half"></i> Chờ duyệt
                                </span>
                            @elseif($leave->status === 'approved')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Đã duyệt
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Đã từ chối
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.leave-requests.show', $leave->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($leave->status === 'pending')
                                    <form action="{{ route('admin.leave-requests.approve', $leave->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-icon" title="Duyệt" style="background: #d1fae5; color: #059669;">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.leave-requests.reject', $leave->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-delete" title="Từ chối" onclick="return confirm('Bạn chắc chắn muốn từ chối đơn này?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $leaveRequests->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có đơn xin phép</h3>
                <p>Hiện chưa có đơn xin phép nào từ nhân viên</p>
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

    // Filter by status
    document.getElementById('statusFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            row.style.display = (filterValue === '' || status === filterValue) ? '' : 'none';
        });
    });

    console.log('✅ Leave requests index loaded');
</script>
@endpush