@extends('layouts.employee')

@section('title', 'Dự án của tôi')

@php
    $pageTitle = 'Dự án của tôi';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Dự án của tôi';
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            font-size: 28px;
            color: #3b82f6;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: #f3f4f6;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .table thead th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .table tbody td {
            padding: 12px 15px;
            color: #111827;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-planning {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-on-hold {
            background: #fee2e2;
            color: #991b1b;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            color: #d1d5db;
            margin-bottom: 15px;
            font-size: 48px;
        }

        .empty-state h3 {
            font-size: 18px;
            color: #374151;
            margin: 0 0 8px 0;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .filter-group select {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            background: white;
            cursor: pointer;
            color: #111827;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-tasks"></i>
        Dự án của tôi
    </h1>
</div>

<!-- Filter -->
<div class="filter-section">
    <div class="filter-group">
        <label>Trạng thái:</label>
        <select onchange="filterProjects(this.value)">
            <option value="">Tất cả</option>
            <option value="In progress">Đang thực hiện</option>
            <option value="Completed">Hoàn thành</option>
            <option value="Suspended">Tạm dừng</option>
        </select>
    </div>
</div>

<!-- Projects List -->
<div class="card">
    <div class="card-header">
        <h5>Danh sách dự án</h5>
    </div>

    <div class="table-container">
        @if($projects->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Dự án</th>
                        <th>Quản lý</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr data-status="{{ $project->status }}">
                            <td>
                                <strong>{{ $project->name }}</strong>
                            </td>
                            <td>{{ $project->manager ? $project->manager->full_name : 'N/A' }}</td>
                            <td>{{ $project->start_date ? $project->start_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $project->end_date ? $project->end_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if($project->progress)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1;">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $project->progress }}%"></div>
                                            </div>
                                        </div>
                                        <span style="font-weight: 600; min-width: 35px;">{{ $project->progress }}%</span>
                                    </div>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $project->status_class }}">
                                    {{ $project->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="{{ route('employee.projects.show', $project->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Chưa có dự án nào</h3>
                <p>Bạn chưa được giao dự án nào</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterProjects(status) {
        const rows = document.querySelectorAll('.table tbody tr');
        rows.forEach(row => {
            if (status === '' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush