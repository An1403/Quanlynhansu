@extends('layouts.admin')

@section('title', 'Quản lý Nhân viên')

@php
    $pageTitle = 'Quản lý Nhân viên';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.employees.index') . '">Nhân viên</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@section('content')
<div class="page-header">
    <h1><i class="fas fa-users"></i> Danh sách Nhân viên</h1>
    <div class="page-actions">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Nhân viên</span>
        </a>
    </div>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, mã NV, email...">
        </div>
        
        <select class="filter-select" id="departmentFilter">
            <option value="">Tất cả phòng ban</option>
            @foreach(\App\Models\Department::all() as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="Active">Active</option>
            <option value="Resigned">Resigned</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($employees->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Giới tính</th>
                        <th>Phòng ban</th>
                        <th>Chức vụ</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Lương CB</th>
                        <th>Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <div class="employee-info">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="employee-avatar">
                                @else
                                    <div class="employee-placeholder">
                                        {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="employee-name">{{ $employee->full_name }}</div>
                                    <div class="employee-code">{{ $employee->employee_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $employee->gender }}</td>
                        <td>{{ $employee->department_name ?? '-' }}</td>
                        <td>{{ $employee->position_name ?? '-' }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ number_format($employee->base_salary, 0, ',', '.') }} đ</td>
                        <td>
                            @if($employee->status === 'Active')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i>đang làm</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-ban"></i> đã nghỉ</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')">
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
                {{ $employees->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-slash fa-2x"></i>
                </div>
                <h3>Chưa có nhân viên</h3>
                <p>Hãy thêm nhân viên đầu tiên cho công ty</p>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    <span>Thêm Nhân viên</span>
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
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter by department
    document.getElementById('departmentFilter').addEventListener('change', function() {
        filterTable();
    });

    // Filter by status
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const deptFilter = document.getElementById('departmentFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            let showRow = true;
            row.style.display = showRow ? '' : 'none';
        });
    }

    console.log('✅ Employees index loaded');
</script>
@endpush
