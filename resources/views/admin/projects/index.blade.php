@extends('layouts.admin')

@section('title', 'Quản lý Dự án')

@php
    $pageTitle = 'Quản lý Dự án';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.projects.index') . '">Dự án</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin: 6px 0;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }

        .progress-text {
            font-size: 12px;
            font-weight: 600;
            color: #3b82f6;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-project-diagram"></i> Danh sách Dự án</h1>
    <div class="page-actions">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Dự án</span>
        </a>
    </div>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên dự án...">
        </div>
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="In progress">Đang thực hiện</option>
            <option value="Completed">Hoàn thành</option>
            <option value="Suspended">Tạm dừng</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($projects->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên Dự án</th>
                        <th>Quản lý</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th style="text-align: center;">Tiến độ</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr data-status="{{ $project->status }}">
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="project-name">{{ $project->name }}</div>
                            </div>
                        </td>
                        <td>
                            @if($project->manager)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                        {{ substr($project->manager->full_name, 0, 1) }}
                                    </div>
                                    <span>{{ $project->manager->full_name }}</span>
                                </div>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($project->start_date)
                                {{ $project->formatted_start_date }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($project->end_date)
                                {{ $project->formatted_end_date }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="min-width: 120px;">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $project->progress ?? 0 }}%"></div>
                                </div>
                                <span class="progress-text">{{ $project->progress ?? 0 }}%</span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($project->status === 'In progress')
                                <span class="badge badge-warning" style="background: #fef3c7; color: #b45309;">
                                    <i class="fas fa-spinner fa-spin"></i> Đang thực hiện
                                </span>
                            @elseif($project->status === 'Completed')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Hoàn thành
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-pause-circle"></i> Tạm dừng
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
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
                {{ $projects->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có dự án</h3>
                <p>Hãy thêm dự án đầu tiên cho công ty</p>
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Dự án</span>
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
            const projectName = row.querySelector('.project-name').textContent.toLowerCase();
            row.style.display = projectName.includes(searchTerm) ? '' : 'none';
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

    console.log('✅ Projects index loaded');
</script>
@endpush