@extends('layouts.employee')

@section('title', 'Hồ sơ cá nhân')

@php
    $pageTitle = 'Hồ sơ cá nhân';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / Hồ sơ cá nhân';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <style>
        .employee-profile {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .profile-sidebar {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            border: 4px solid #e5e7eb;
        }

        .profile-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 64px;
            font-weight: 700;
            margin: 0 auto 20px;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 5px;
        }

        .profile-code {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .stat-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .profile-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .info-card h3 {
            font-size: 18px;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
        }

        .action-bar {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .employee-profile {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user"></i> Hồ sơ cá nhân</h1>
    <div class="page-actions">
        <a href="{{ route('employee.profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            <span>Chỉnh sửa</span>
        </a>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<div class="employee-profile">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        @if($employee->photo)
            <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ Auth::user()->full_name }}" class="profile-photo">
        @else
            <div class="profile-placeholder">
                {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
            </div>
        @endif

        <div class="profile-name">{{ $employee->full_name }}</div>
        <div class="profile-code">{{ $employee->employee_code }}</div>

        @if($employee->status === 'Active')
            <span class="badge badge-success" style="font-size: 14px; padding: 6px 16px;">
                <i class="fas fa-check-circle"></i> Đang làm việc
            </span>
        @else
            <span class="badge badge-danger" style="font-size: 14px; padding: 6px 16px;">
                <i class="fas fa-times-circle"></i> Đã nghỉ việc
            </span>
        @endif

        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-value">
                    {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->age : '-' }}
                </div>
                <div class="stat-label">Tuổi</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    {{ $employee->join_date? round(\Carbon\Carbon::parse($employee->join_date)->diffInDays(now()) / 365): 0 }}
                </div>
                <div class="stat-label">Năm làm việc</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="profile-main">
        <!-- Thông tin cá nhân -->
        <div class="info-card">
            <h3>
                <i class="fas fa-info-circle"></i>
                Thông tin cá nhân
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-venus-mars"></i> Giới tính</div>
                    <div class="info-value">{{ $employee->gender === 'Nam' ? 'Nam' : ($employee->gender === 'Nữ' ? 'Nữ' : '-') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-birthday-cake"></i> Ngày sinh</div>
                    <div class="info-value">
                        {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-phone"></i> Số điện thoại</div>
                    <div class="info-value">{{ $employee->phone ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                    <div class="info-value">{{ $employee->email ?? '-' }}</div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</div>
                    <div class="info-value">{{ $employee->address ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Căn cước công dân -->
        <div class="info-card">
            <h3>
                <i class="fas fa-passport"></i>
                Thông tin căn cước công dân
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-id-card"></i> Số căn cước</div>
                    <div class="info-value">{{ $employee->identity_card ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Nơi cấp</div>
                    <div class="info-value">{{ $employee->identity_card_issued_at ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar"></i> Ngày cấp</div>
                    <div class="info-value">
                        {{ $employee->identity_card_date ? \Carbon\Carbon::parse($employee->identity_card_date)->format('d/m/Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin công việc -->
        <div class="info-card">
            <h3>
                <i class="fas fa-briefcase"></i>
                Thông tin công việc
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-building"></i> Phòng ban</div>
                    <div class="info-value">{{ $employee->department->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-user-tie"></i> Chức vụ</div>
                    <div class="info-value">{{ $employee->position->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar-check"></i> Ngày vào làm</div>
                    <div class="info-value">
                        {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-money-bill-wave"></i> Lương cơ bản</div>
                    <div class="info-value" style="color: #10b981; font-weight: 700;">
                        {{ number_format($employee->base_salary, 0, ',', '.') }} đ
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="info-card">
            <h3>
                <i class="fas fa-chart-line"></i>
                Thống kê (Tháng này)
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-clock"></i> Tổng ngày công</div>
                    <div class="info-value">
                        {{ $statistics['worked_days'] ?? 0 }} ngày
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-business-time"></i> Tổng giờ làm</div>
                    <div class="info-value">
                        {{ $statistics['total_hours'] ?? 0 }} giờ
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar-times"></i> Ngày vắng mặt</div>
                    <div class="info-value" style="color: #ef4444;">
                        {{ $statistics['absent_days'] ?? 0 }} ngày
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-hourglass-end"></i> Đi muộn</div>
                    <div class="info-value" style="color: #f59e0b;">
                        {{ $statistics['late_times'] ?? 0 }} lần
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    console.log('✅ Employee profile view loaded');
</script>
@endpush