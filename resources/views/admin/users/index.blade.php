@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản')

@php
    $pageTitle = 'Quản lý Tài khoản';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.users.index') . '">Tài khoản</a> / Danh sách';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-lock"></i> Quản lý Tài khoản</h1>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #10b981; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-times-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, email, username...">
        </div>
        
        <select class="filter-select" id="roleFilter">
            <option value="">Tất cả vai trò</option>
            <option value="admin">Quản trị viên</option>
            <option value="employee">Nhân viên</option>
            <option value="accountant">Kế toán</option>
        </select>
        
        <select class="filter-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="1">Hoạt động</option>
            <option value="0">Khóa</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($users->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Tài khoản</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="employee-name">{{ $user->username }}</div>
                                    <div class="employee-code">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->full_name ?? '-' }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>
                            <span class="badge" style="
                                @if($user->role == 'admin')
                                    background: #fee2e2; color: #991b1b;
                                @elseif($user->role == 'accountant')
                                    background: #fef3c7; color: #92400e;
                                @else
                                    background: #dbeafe; color: #1e40af;
                                @endif
                            ">
                                @if($user->role == 'admin')
                                    <i class="fas fa-shield-alt"></i> Quản trị viên
                                @elseif($user->role == 'accountant')
                                    <i class="fas fa-calculator"></i> Kế toán
                                @else
                                    <i class="fas fa-user"></i> Nhân viên
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($user->status == 1)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Hoạt động
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-ban"></i> Khóa
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
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
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-slash fa-2x"></i>
                </div>
                <h3>Chưa có tài khoản</h3>
                <p>Hiện chưa có tài khoản nào trong hệ thống</p>
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

    // Filter by role
    document.getElementById('roleFilter').addEventListener('change', function() {
        filterTable();
    });

    // Filter by status
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const roleCell = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const statusCell = row.querySelector('td:nth-child(5)').textContent;
            
            let showRow = true;
            
            if (roleFilter) {
                const roleMap = {
                    'admin': 'quản trị viên',
                    'employee': 'nhân viên',
                    'accountant': 'kế toán'
                };
                if (!roleCell.includes(roleMap[roleFilter])) {
                    showRow = false;
                }
            }
            
            if (statusFilter) {
                const isActive = statusCell.includes('Hoạt động');
                if (statusFilter == '1' && !isActive) showRow = false;
                if (statusFilter == '0' && isActive) showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }

    console.log('✅ Users index loaded');
</script>
@endpush