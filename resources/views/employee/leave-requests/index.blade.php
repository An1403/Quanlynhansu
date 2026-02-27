@extends('layouts.employee')

@section('title', 'Đơn xin nghỉ')

@php
    $pageTitle = 'Đơn xin nghỉ';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Đơn xin nghỉ';
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

        .stat-icon.primary { background: #667eea; }
        .stat-icon.success { background: #10b981; }
        .stat-icon.warning { background: #f59e0b; }
        .stat-icon.danger { background: #ef4444; }

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

        .card-header {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px;
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
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

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
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

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-file-alt"></i>
        Đơn xin nghỉ
    </h1>
    <div class="page-actions">
        <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Tạo đơn mới</span>
        </a>
    </div>
</div>

<!-- Thống kê -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <h6>Số dư năm nay</h6>
            <h4>{{ $statistics['remaining'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h6>Đã duyệt</h6>
            <h4>{{ $statistics['approved'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-content">
            <h6>Chờ duyệt</h6>
            <h4>{{ $statistics['pending'] ?? 0 }}</h4>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <h6>Từ chối</h6>
            <h4>{{ $statistics['rejected'] ?? 0 }}</h4>
        </div>
    </div>
</div>

<!-- Bảng đơn xin nghỉ -->
<div class="card">
    <div class="card-header">
        <h5>Danh sách đơn xin nghỉ</h5>
    </div>

    <div class="table-container">
        @if($leaveRequests->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Loại nghỉ</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Lý do</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveRequests as $request)
                    <tr>
                        <td><strong>{{ $request->request_id }}</strong></td>
                        <td>{{ $request->leaveType?->name ?? 'N/A' }}</td>
                        <td>{{ $request->formatted_start_date }}</td>
                        <td>{{ $request->formatted_end_date }}</td>
                        <td>{{ Str::limit($request->reason, 50) ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($request->status === 'pending')
                                <span class="badge badge-pending">
                                    <i class="fas fa-hourglass-half"></i> Chờ duyệt
                                </span>
                            @elseif($request->status === 'approved')
                                <span class="badge badge-approved">
                                    <i class="fas fa-check-circle"></i> Đã duyệt
                                </span>
                            @else
                                <span class="badge badge-rejected">
                                    <i class="fas fa-times-circle"></i> Từ chối
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('employee.leave-requests.show', $request->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($request->canEdit())
                                    <a href="{{ route('employee.leave-requests.edit', $request->id) }}" class="btn-icon btn-edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('employee.leave-requests.destroy', $request->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Hủy">
                                            <i class="fas fa-trash-alt"></i>
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
                    <i class="fas fa-inbox fa-3x"></i>
                </div>
                <h3>Chưa có đơn xin nghỉ</h3>
                <p>Hãy tạo đơn xin nghỉ đầu tiên</p>
                <a href="{{ route('employee.leave-requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Tạo đơn mới</span>
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    console.log('✅ Leave requests index loaded');
</script>
@endpush