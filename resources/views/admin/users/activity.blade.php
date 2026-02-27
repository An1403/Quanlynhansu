@extends('layouts.admin')

@section('title', 'Nhật ký Hoạt động')

@php
    $pageTitle = 'Nhật ký Hoạt động';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.users.index') . '">Tài khoản</a> / Nhật ký';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            position: relative;
            padding-left: 40px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 10px;
            height: 10px;
            background: #3b82f6;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 10px;
            width: 2px;
            height: calc(100% + 20px);
            background: #e5e7eb;
        }

        .timeline-item.warning::before {
            background: #f59e0b;
            box-shadow: 0 0 0 2px #f59e0b;
        }

        .timeline-item.danger::before {
            background: #ef4444;
            box-shadow: 0 0 0 2px #ef4444;
        }

        .timeline-item.success::before {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }

        .timeline-item.create::before {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }

        .timeline-item.update::before {
            background: #3b82f6;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        .timeline-item.delete::before {
            background: #ef4444;
            box-shadow: 0 0 0 2px #ef4444;
        }

        .timeline-item.login::before {
            background: #f59e0b;
            box-shadow: 0 0 0 2px #f59e0b;
        }

        .timeline-item.logout::before {
            background: #6b7280;
            box-shadow: 0 0 0 2px #6b7280;
        }

        .timeline-item.export::before {
            background: #8b5cf6;
            box-shadow: 0 0 0 2px #8b5cf6;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-time {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            color: #111827;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timeline-description {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
            line-height: 1.6;
        }

        .activity-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-create {
            background: #dcfce7;
            color: #166534;
        }

        .badge-update {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-login {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-logout {
            background: #f3f4f6;
            color: #374151;
        }

        .badge-export {
            background: #ede9fe;
            color: #5b21b6;
        }

        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-bar input,
        .filter-bar select {
            padding: 10px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-bar input:focus,
        .filter-bar select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .info-section {
            margin-top: 30px;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 8px;
        }

        .info-section h4 {
            margin-top: 0;
            color: #111827;
            font-size: 16px;
        }

        .info-section ul {
            margin: 10px 0;
            padding-left: 20px;
            color: #6b7280;
            font-size: 14px;
        }

        .info-section li {
            margin-bottom: 8px;
        }

        /* ✅ Custom Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-info {
            color: #6b7280;
            font-size: 14px;
        }

        .pagination-links {
            display: flex;
            gap: 5px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination-links li {
            display: inline-block;
        }

        .pagination-links a,
        .pagination-links span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #374151;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .pagination-links a:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .pagination-links .active span {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            font-weight: 600;
        }

        .pagination-links .disabled span {
            color: #d1d5db;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .pagination-links .disabled:hover span {
            background: #f9fafb;
            border-color: #e5e7eb;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-history"></i> Nhật ký Hoạt động</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<div class="card">
    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #92400e; font-size: 14px;">
        <i class="fas fa-info-circle"></i> Theo dõi các hoạt động của người dùng trong hệ thống
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div style="flex: 1; min-width: 250px; position: relative; display: flex; align-items: center;">
            <i class="fas fa-search" style="position: absolute; left: 12px; color: #9ca3af;"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên người dùng..." style="padding-left: 38px; width: 100%;">
        </div>
        
        <select id="actionFilter">
            <option value="">Tất cả hành động</option>
            <option value="create">Tạo mới</option>
            <option value="update">Cập nhật</option>
            <option value="delete">Xóa</option>
            <option value="login">Đăng nhập</option>
            <option value="logout">Đăng xuất</option>
            <option value="export">Xuất file</option>
        </select>
    </div>

    @if($activities->count() > 0)
        <div class="timeline">
            @foreach($activities as $activity)
            <div class="timeline-item {{ strtolower($activity->action) }}" data-action="{{ strtolower($activity->action) }}">
                <div class="timeline-content">
                    <div class="timeline-time">
                        <i class="fas fa-clock"></i> {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </div>
                    <div class="timeline-title">
                        @if(strtolower($activity->action) == 'create')
                            <span class="activity-badge badge-create"><i class="fas fa-plus-circle"></i> Tạo mới</span>
                        @elseif(strtolower($activity->action) == 'update')
                            <span class="activity-badge badge-update"><i class="fas fa-edit"></i> Cập nhật</span>
                        @elseif(strtolower($activity->action) == 'delete')
                            <span class="activity-badge badge-delete"><i class="fas fa-trash"></i> Xóa</span>
                        @elseif(strtolower($activity->action) == 'login')
                            <span class="activity-badge badge-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</span>
                        @elseif(strtolower($activity->action) == 'logout')
                            <span class="activity-badge badge-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</span>
                        @elseif(strtolower($activity->action) == 'export')
                            <span class="activity-badge badge-export"><i class="fas fa-file-download"></i> Xuất file</span>
                        @else
                            <span class="activity-badge">{{ $activity->action }}</span>
                        @endif
                    </div>
                    <div class="timeline-description">
                        <strong>Người dùng:</strong> {{ $activity->user->username ?? 'N/A' }}
                        <br>
                        <strong>Mô tả:</strong> {{ $activity->description ?? 'Không có mô tả' }}
                        <br>
                        <small style="color: #9ca3af;">
                            <i class="fas fa-network-wired"></i> IP: {{ $activity->ip_address ?? 'N/A' }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- ✅ Custom Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                Hiển thị {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} 
                trong tổng số {{ $activities->total() }} bản ghi
            </div>
            <nav>
                <ul class="pagination-links">
                    {{-- Previous Page Link --}}
                    @if ($activities->onFirstPage())
                        <li class="disabled">
                            <span><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $activities->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($activities->getUrlRange(1, $activities->lastPage()) as $page => $url)
                        @if ($page == $activities->currentPage())
                            <li class="active">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($activities->hasMorePages())
                        <li>
                            <a href="{{ $activities->nextPageUrl() }}" rel="next">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="disabled">
                            <span><i class="fas fa-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox fa-2x"></i>
            </div>
            <h3>Chưa có hoạt động</h3>
            <p>Các hoạt động sẽ xuất hiện ở đây khi người dùng thực hiện các hành động trong hệ thống</p>
        </div>
    @endif
</div>

<!-- Info Section -->
<div class="info-section">
    <h4><i class="fas fa-lightbulb"></i> Thông tin</h4>
    <ul>
        <li>Nhật ký hoạt động sẽ ghi lại các hành động quan trọng như đăng nhập, tạo/sửa/xóa dữ liệu</li>
        <li>Dữ liệu nhật ký thường được lưu giữ trong 90 ngày</li>
        <li>Bạn có thể xuất dữ liệu nhật ký để phân tích hoặc kiểm toán</li>
        <li>Sử dụng bộ lọc và tìm kiếm để theo dõi hoạt động của người dùng cụ thể</li>
    </ul>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.timeline-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter by action
    document.getElementById('actionFilter').addEventListener('change', function() {
        const actionFilter = this.value.toLowerCase();
        const items = document.querySelectorAll('.timeline-item');
        
        items.forEach(item => {
            let showItem = true;
            
            if (actionFilter) {
                const itemAction = item.getAttribute('data-action');
                showItem = itemAction === actionFilter;
            }
            
            item.style.display = showItem ? '' : 'none';
        });
    });

    console.log('✅ Activity log page loaded');
</script>
@endpush