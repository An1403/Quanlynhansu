@extends('layouts.admin')

@section('title', 'Quản lý Chức vụ')

@php
    $pageTitle = 'Quản lý Chức vụ';
    $breadcrumb = '<a href="' . route('admin.dashboard') . '">Home</a> / <a href="' . route('admin.positions.index') . '">Chức vụ</a>';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-briefcase"></i> Danh sách Chức vụ</h1>
    <div class="page-actions">
        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Thêm Chức vụ</span>
        </a>
    </div>
</div>

<div class="card">
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên chức vụ...">
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($positions->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Chức vụ</th>
                        <th style="text-align: center;">Phụ cấp</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $position)
                    <tr>
                        <td>
                            <div class="employee-info">
                                <div class="employee-placeholder" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="position-name">{{ $position->name }}</div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: 600; color: #2c3e50;">
                                {{ number_format($position->allowance, 0, ',', '.') }} đ
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($position->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('admin.positions.show', $position->id) }}" class="btn-icon btn-view" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chức vụ này?')">
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
                {{ $positions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox fa-2x"></i>
                </div>
                <h3>Chưa có chức vụ</h3>
                <p>Hãy thêm chức vụ đầu tiên cho công ty</p>
                <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Chức vụ</span>
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
            const positionName = row.querySelector('.position-name').textContent.toLowerCase();
            row.style.display = positionName.includes(searchTerm) ? '' : 'none';
        });
    });

    console.log('✅ Positions index loaded');
</script>
@endpush