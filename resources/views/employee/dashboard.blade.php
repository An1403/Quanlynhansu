@extends('layouts.employee')

@section('title', 'Dashboard Nhân Viên')

@php
    $pageTitle = 'Dashboard';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Dashboard';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
        <div class="stat-value">{{ $stats['attendance_count'] }}</div>
        <div class="stat-label">Ngày Đi Làm</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
        <div class="stat-value">{{ $stats['leave_count'] }}</div>
        <div class="stat-label">Ngày Nghỉ Phép</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-diagram-project"></i></div>
        <div class="stat-value">{{ $stats['assigned_projects'] }}</div>
        <div class="stat-label">Dự Án Được Giao</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
        <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
        <div class="stat-label">Công Việc Chưa Hoàn</div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Personal Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-regular fa-id-card"></i> Thông Tin Cá Nhân</h3>
            <a href="{{ route('employee.profile.edit') }}" class="card-action">Chỉnh sửa <i class="fa-solid fa-pen"></i></a>
        </div>
        
        <div class="info-section">
            <div class="info-group">
                <label>Mã Nhân Viên:</label>
                <span>{{ $employee->employee_code }}</span>
            </div>
            <div class="info-group">
                <label>Họ Tên:</label>
                <span>{{ $employee->full_name }}</span>
            </div>
            <div class="info-group">
                <label>Phòng Ban:</label>
                <span>{{ $employee->department_name ?? 'Chưa phân' }}</span>
            </div>
            <div class="info-group">
                <label>Chức Vụ:</label>
                <span>{{ $employee->position?->name ?? '-' }}</span>
            </div>
            <div class="info-group">
                <label>Email:</label>
                <span>{{ $employee->email }}</span>
            </div>
            <div class="info-group">
                <label>Điện Thoại:</label>
                <span>{{ $employee->phone ?? 'Chưa cập nhật' }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Leave Requests -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-regular fa-calendar-xmark"></i> Đơn Xin Nghỉ Gần Đây</h3>
            <a href="{{ route('employee.leave-requests.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        @if($recent_leaves->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Từ Ngày</th>
                        <th>Đến Ngày</th>
                        <th>Loại Nghỉ</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_leaves as $leave)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($leave->start_date)) }}</td>
                        <td>{{ date('d/m/Y', strtotime($leave->end_date)) }}</td>
                        <td>{{ $leave->leave_type }}</td>
                        <td>
                            @php
                                $statusClass = match($leave->status) {
                                    'approved' => 'badge-success',
                                    'rejected' => 'badge-danger',
                                    'pending' => 'badge-warning',
                                    default => 'badge-secondary'
                                };
                                $statusText = match($leave->status) {
                                    'approved' => 'Đã duyệt',
                                    'rejected' => 'Bị từ chối',
                                    'pending' => 'Chờ duyệt',
                                    default => 'Không xác định'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-regular fa-envelope-open"></i>
                <p>Chưa có đơn xin nghỉ</p>
            </div>
        @endif
    </div>
</div>

<!-- Assigned Projects -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-diagram-project"></i> Dự Án Được Giao</h3>
        <a href="{{ route('employee.projects.index') }}" class="card-action">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    @if($assigned_projects->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Tên Dự Án</th>
                    <th>Địa Điểm</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Tiến Độ</th>
                    <th>Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assigned_projects as $project)
                <tr>
                    <td><strong>{{ $project->name }}</strong></td>
                    <td>{{ $project->location }}</td>
                    <td>{{ date('d/m/Y', strtotime($project->end_date)) }}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $project->progress ?? 0 }}%"></div>
                        </div>
                        <span class="progress-text">{{ $project->progress ?? 0 }}%</span>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $project->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-folder-open"></i>
            <p>Chưa có dự án được giao</p>
        </div>
    @endif
</div>



<!-- Quick Actions -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-rocket"></i> Các Thao Tác Nhanh</h3>
    </div>
    
    <div class="quick-actions">
        <a href="{{ route('employee.leave-requests.create') }}" class="action-btn">
            <i class="fa-solid fa-plus"></i>
            <span>Tạo Đơn Xin Nghỉ</span>
        </a>
        <a href="{{ route('employee.attendance.index') }}" class="action-btn">
            <i class="fa-solid fa-clipboard-check"></i>
            <span>Xem Chấm Công</span>
        </a>
        <a href="{{ route('employee.profile.edit') }}" class="action-btn">
            <i class="fa-solid fa-user"></i>
            <span>Cập Nhật Hồ Sơ</span>
        </a>
        <a href="{{ route('employee.salary-slip.index') }}" class="action-btn">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Phiếu Lương</span>
        </a>
    </div>
</div>

@endsection

<script>
    console.log('Employee ID: {{ $employee->id ?? "NULL" }}');
    console.log('Employee Name: {{ $employee->full_name ?? "NULL" }}');
</script>
@push('scripts')
    <script src="{{ asset('js/employee/dashboard.js') }}"></script>
@endpush