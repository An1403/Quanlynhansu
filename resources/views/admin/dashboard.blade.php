@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@php
    $pageTitle = 'Dashboard';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / Dashboard';
    $notificationCount = $stats['pending_leaves'] ?? 0;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <style>
        /* ===== CHART STYLES ===== */
        .chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 15px;
            padding: 30px 20px 20px 20px;
            min-height: 280px;
            background: linear-gradient(180deg, #f9fafb 0%, #ffffff 100%);
            border-radius: 8px;
        }

        .chart-bar {
            width: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px 8px 0 0;
            height: var(--bar-height, 50px);
            min-height: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
            position: relative;
            cursor: pointer;
        }

        .chart-bar:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(102, 126, 234, 0.3);
        }

        .chart-bar::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.2s;
            white-space: nowrap;
            pointer-events: none;
        }

        .chart-bar:hover::before {
            opacity: 1;
        }

        .chart-label {
            margin-top: 10px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            line-height: 1.4;
        }

        .chart-label strong {
            font-size: 16px;
            display: block;
            margin-top: 3px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: #6b7280;
            font-size: 18px;
            margin: 10px 0;
        }

        .empty-state p {
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
<!-- Stats Cards - Row 1 -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">{{ $stats['total_employees'] }}</div>
        <div class="stat-label">Tổng Nhân Viên</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
        <div class="stat-value">{{ $stats['today_attendance'] }}</div>
        <div class="stat-label">Đi Làm Hôm Nay</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-diagram-project"></i></div>
        <div class="stat-value">{{ $stats['active_projects'] }}</div>
        <div class="stat-label">Dự Án Đang Chạy</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-pen"></i></div>
        <div class="stat-value">{{ $stats['pending_leaves'] }}</div>
        <div class="stat-label">Đơn Chờ Duyệt</div>
    </div>
</div>


<!-- Content Grid -->
<div class="content-grid">
    <!-- Recent Employees -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-regular fa-id-card"></i> Nhân Viên Mới Nhất</h3>
            <a href="{{ route('admin.employees.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($recent_employees->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Họ Tên</th>
                        <th>Phòng Ban</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_employees as $emp)
                    <tr>
                        <td><strong>{{ $emp->employee_code }}</strong></td>
                        <td>{{ $emp->full_name }}</td>
                        <td>{{ $emp->department_name ?? 'Chưa phân' }}</td>
                        <td>
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> {{ $emp->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-regular fa-envelope-open"></i>
                <p>Chưa có nhân viên nào</p>
            </div>
        @endif
    </div>

    <!-- Pending Leave Requests -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-regular fa-calendar-xmark"></i> Đơn Xin Nghỉ Chờ Duyệt</h3>
            <a href="{{ route('admin.leave-requests.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($pending_leave_requests->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhân Viên</th>
                        <th>Từ Ngày</th>
                        <th>Đến Ngày</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending_leave_requests as $leave)
                    <tr>
                        <td>{{ $leave->full_name }}</td>
                        <td>{{ date('d/m/Y', strtotime($leave->start_date)) }}</td>
                        <td>{{ date('d/m/Y', strtotime($leave->end_date)) }}</td>
                        <td>
                            <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Chờ duyệt</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-circle-check"></i>
                <p>Không có đơn chờ duyệt</p>
            </div>
        @endif
    </div>
</div>

<!-- Attendance Chart -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-chart-line"></i> Thống Kê Chấm Công (7 Ngày Gần Nhất)</h3>
    </div>
    
    @if($attendance_chart->count() > 0)
        <div class="chart-container">
            @foreach($attendance_chart as $data)
                @php
                    $maxEmployees = max(1, $stats['total_employees']);
                    $barHeight = $data->total > 0 ? ($data->total / $maxEmployees) * 200 : 5; 
                @endphp
                <div style="text-align: center;">
                    <div class="chart-bar" 
                         style="--bar-height: {{ $barHeight }}px; {{ $data->total == 0 ? 'background: #e5e7eb;' : '' }}"
                         title="{{ $data->day_name }}: {{ $data->total }} người">
                    </div>
                    <div class="chart-label">
                        {{ date('d/m', strtotime($data->date)) }}<br>
                        <small style="color: #9ca3af; font-size: 11px;">{{ substr($data->day_name, 0, 2) }}</small><br>
                        <strong style="color: {{ $data->total > 0 ? '#111827' : '#d1d5db' }}">{{ $data->total }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Thêm legend -->
        <div style="padding: 15px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 20px; font-size: 13px; color: #6b7280;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 3px;"></div>
                    <span>Có mặt</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #e5e7eb; border-radius: 3px;"></div>
                    <span>Không có dữ liệu</span>
                </div>
            </div>
            <div style="font-size: 13px; color: #9ca3af;">
                <i class="fa-solid fa-users"></i> Tổng: <strong style="color: #111827;">{{ $stats['total_employees'] }}</strong> nhân viên
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-chart-simple"></i>
            <p>Chưa có dữ liệu chấm công</p>
        </div>
    @endif
</div>

<!-- Employee Status Summary (Thêm mới) -->
<div class="content-grid" style="margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-pie"></i> Thống Kê Trạng Thái Nhân Viên</h3>
        </div>
        
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @php
                    $activeEmployees = $stats['active_employees'] ?? 0;
                    $totalEmployees = $stats['total_employees'] ?? 1;
                    $activePercent = $totalEmployees > 0 ? ($activeEmployees / $totalEmployees) * 100 : 0;
                    $resignedPercent = $totalEmployees > 0 ? (($totalEmployees - $activeEmployees) / $totalEmployees) * 100 : 0;
                @endphp
                
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 600; color: #111827;">Đang làm việc</span>
                        <span style="color: #10b981; font-weight: 600;">{{ $activeEmployees }} người ({{ round($activePercent, 1) }}%)</span>
                    </div>
                    <div style="background: #f0fdf4; border-radius: 8px; height: 24px; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #10b981, #059669); height: 100%; width: {{ $activePercent }}%; transition: width 0.3s ease;"></div>
                    </div>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 600; color: #111827;">Đã nghỉ</span>
                        <span style="color: #ef4444; font-weight: 600;">{{ $totalEmployees - $activeEmployees }} người ({{ round($resignedPercent, 1) }}%)</span>
                    </div>
                    <div style="background: #fef2f2; border-radius: 8px; height: 24px; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #ef4444, #dc2626); height: 100%; width: {{ $resignedPercent }}%; transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Summary (Thêm mới) -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-building"></i> Nhân Viên Theo Phòng Ban</h3>
            <a href="{{ route('admin.employees.index') }}" class="card-action">Chi tiết <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($employees_by_department && count($employees_by_department) > 0)
            <div style="padding: 20px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($employees_by_department as $dept)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #374151; font-weight: 500;">{{ $dept->department_name ?? 'Chưa phân phòng' }}</span>
                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                            {{ $dept->employee_count }} người
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <p>Chưa có dữ liệu phòng ban</p>
            </div>
        @endif
    </div>
</div>

<!-- Upcoming Projects -->
@if($upcoming_projects->count() > 0)
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Dự Án Sắp Hết Hạn</h3>
        <a href="{{ route('admin.projects.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Tên Dự Án</th>
                <th>Địa Điểm</th>
                <th>Ngày Kết Thúc</th>
                <th>Còn Lại</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($upcoming_projects as $project)
            <tr>
                <td><strong>{{ $project->name }}</strong></td>
                <td>{{ $project->location }}</td>
                <td>{{ date('d/m/Y', strtotime($project->end_date)) }}</td>
                <td>
                    @php
                        $endDate = \Carbon\Carbon::parse($project->end_date);
                        $days = $endDate->diffInDays(now());
                    @endphp
                    <span class="badge {{ $days <= 7 ? 'badge-danger' : 'badge-warning' }}">
                        <i class="fa-solid fa-clock"></i> {{ $days }} ngày
                    </span>
                </td>
                <td>
                    <span class="badge badge-info"><i class="fa-solid fa-clipboard-check"></i> {{ $project->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Activities Log (Thêm mới) -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-history"></i> Hoạt Động Gần Đây</h3>
        <a href="{{ route('admin.users.activity') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    @if($recent_activities && $recent_activities->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Hành Động</th>
                    <th>Người Dùng</th>
                    <th>Mô Tả</th>
                    <th>Thời Gian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_activities as $activity)
                <tr>
                    <td>
                        @if($activity->action == 'create')
                            <span class="badge" style="background: #dcfce7; color: #166534;"><i class="fa-solid fa-plus"></i> Tạo</span>
                        @elseif($activity->action == 'update')
                            <span class="badge" style="background: #dbeafe; color: #1e40af;"><i class="fa-solid fa-edit"></i> Cập nhật</span>
                        @elseif($activity->action == 'delete')
                            <span class="badge" style="background: #fee2e2; color: #991b1b;"><i class="fa-solid fa-trash"></i> Xóa</span>
                        @else
                            <span class="badge" style="background: #f3f4f6; color: #6b7280;">{{ $activity->action }}</span>
                        @endif
                    </td>
                    <td>{{ $activity->full_name ?? $activity->username ?? 'N/A' }}</td>
                    <td style="font-size: 12px; color: #6b7280;">{{ Str::limit($activity->description, 50) }}</td>
                    <td style="font-size: 12px; color: #9ca3af;">
                        @php
                            $createdAt = is_string($activity->created_at) 
                                ? \Carbon\Carbon::parse($activity->created_at) 
                                : $activity->created_at;
                        @endphp
                        {{ $createdAt->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-inbox"></i>
            <p>Chưa có hoạt động nào</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/dashboard.js') }}"></script>
@endpush