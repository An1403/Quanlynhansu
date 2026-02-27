@extends('layouts.admin')

@section('title', 'Quản lý Phòng ban')

@php
    $pageTitle = 'Quản lý Phòng ban';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.departments.index') . '">Phòng ban</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/departments.css') }}">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-sitemap"></i> Danh sách Phòng ban</h1>
    <div class="page-actions">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Phòng ban</span>
        </a>
    </div>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên phòng ban...">
        </div>
        
        <select class="filter-select" id="employeeCountFilter">
            <option value="">Tất cả phòng ban</option>
            <option value="0">Không có nhân viên</option>
            <option value="1-5">1-5 nhân viên</option>
            <option value="6-10">6-10 nhân viên</option>
            <option value="10+">Trên 10 nhân viên</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($departments->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên Phòng ban</th>
                        <th>Mô tả</th>
                        <th style="text-align: center;">Số Nhân viên</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                    <tr data-employee-count="{{ $department->employee_count }}">
                        <td>
                            <div class="department-info">
                                <div class="department-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="department-name">{{ $department->name }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="description" title="{{ $department->description ?? 'Không có mô tả' }}">
                                {{ $department->description ? (strlen($department->description) > 50 ? substr($department->description, 0, 50) . '...' : $department->description) : '-' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info">
                                <i class="fas fa-users"></i>
                                {{ $department->employee_count }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($department->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.departments.show', $department->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete({{ $department->employee_count }})">
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
                {{ $departments->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có phòng ban</h3>
                <p>Hãy thêm phòng ban đầu tiên cho công ty</p>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Phòng ban</span>
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
            const departmentName = row.querySelector('.department-name').textContent.toLowerCase();
            const description = row.querySelector('.description').textContent.toLowerCase();
            const isVisible = departmentName.includes(searchTerm) || description.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
        });
    });

    

    // Confirm delete
    function confirmDelete(employeeCount) {
        if (employeeCount > 0) {
            alert(`Không thể xóa! Phòng ban này có ${employeeCount} nhân viên.`);
            return false;
        }
        return confirm('Bạn có chắc chắn muốn xóa phòng ban này?');
    }

    console.log('✅ Departments index loaded');
</script>
@endpush