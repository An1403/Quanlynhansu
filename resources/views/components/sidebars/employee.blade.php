<!-- Load CSS nếu chưa load trong layout -->
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/sidebar-admin.css') }}">
@endpush

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo"><i data-lucide="user-circle"></i></div>
        <h3>{{ Auth::user()->full_name ?? 'Nhân Viên' }}</h3>
        <p>Quản Lý Cá Nhân</p>
    </div>

    <div class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-section">
            <div class="menu-title">Tổng quan</div>
            <a href="{{ route('employee.dashboard') }}" class="menu-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Thông tin cá nhân -->
        <div class="menu-section">
            <div class="menu-title">Thông tin</div>
            <a href="{{ route('employee.profile.index') }}" class="menu-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
                <i data-lucide="user"></i>
                <span>Hồ sơ cá nhân</span>
            </a>
            
        </div>

        <!-- Chấm công -->
        <div class="menu-section">
            <div class="menu-title">Chấm công</div>
            <a href="{{ route('employee.attendance.index') }}" class="menu-item">
                <i data-lucide="check-circle"></i>
                <span>Chấm công hôm nay</span>
            </a>
        </div>

        <!-- Đơn xin nghỉ -->
        <div class="menu-section">
            <div class="menu-title">Nghỉ phép</div>
            <a href="{{ route('employee.leave-requests.index') }}" class="menu-item {{ request()->routeIs('employee.leave-requests.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>Đơn xin nghỉ</span>
            </a>
            <a href="{{ route('employee.leave-requests.create') }}" class="menu-item">
                <i data-lucide="plus"></i>
                <span>Tạo đơn mới</span>
            </a>
        </div>

        <!-- Dự án -->
        <div class="menu-section">
            <div class="menu-title">Công việc</div>
            <a href="{{ route('employee.projects.index') }}" class="menu-item {{ request()->routeIs('employee.projects.*') ? 'active' : '' }}">
                <i data-lucide="folder-kanban"></i>
                <span>Dự án được giao</span>
            </a>
        </div>

        <!-- Lương -->
        <div class="menu-section">
            <div class="menu-title">Tài chính</div>
            <a href="{{ route('employee.salary-slip.index') }}" class="menu-item {{ request()->routeIs('employee.salary-slip.*') ? 'active' : '' }}">
                <i data-lucide="wallet"></i>
                <span>Phiếu lương</span>
            </a>
        </div>

        <!-- Thông báo -->
        <div class="menu-section">
            <div class="menu-title">Khác</div>
            <a href="#" class="menu-item">
                <i data-lucide="bell"></i>
                <span>Thông báo</span>
            </a>
        </div>
    </div>
</div>

<button class="sidebar-toggle" onclick="toggleSidebar()">
    <i data-lucide="menu"></i>
</button>

<!-- Load Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    lucide.createIcons();

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.sidebar-toggle');
        
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>