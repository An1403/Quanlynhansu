<!-- Load CSS nếu chưa load trong layout -->
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/sidebar-admin.css') }}">
@endpush

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo"><i data-lucide="building"></i></div>
        <h3>Công Ty Xây Dựng Thành An</h3>
        <p>Quản Trị Hệ Thống</p>
    </div>

    <div class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-section">
            <div class="menu-title">Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Quản lý nhân sự -->
        <div class="menu-section">
            <div class="menu-title">Quản lý nhân sự</div>
            <a href="{{ route('admin.employees.index') }}" class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Nhân viên</span>
            </a>
            <a href="{{ route('admin.departments.index') }}" class="menu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i data-lucide="building-2"></i>
                <span>Phòng ban</span>
            </a>
            <a href="{{ route('admin.positions.index') }}" class="menu-item {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                <i data-lucide="briefcase"></i>
                <span>Chức vụ</span>
            </a>
        </div>

        <!-- Công việc -->
        <div class="menu-section">
            <div class="menu-title">Công việc</div>
            <a href="{{ route('admin.projects.index') }}" class="menu-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <i data-lucide="folder-kanban"></i>
                <span>Dự án</span>
            </a>
            <a href="{{ route('admin.attendances.index') }}" class="menu-item {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                <i data-lucide="clock-9"></i>
                <span>Chấm công</span>
            </a>
        </div>

        <!-- Tài chính -->
        <div class="menu-section">
            <div class="menu-title">Tài chính</div>
            <a href="{{ route('admin.salaries.index') }}" class="menu-item {{ request()->routeIs('admin.salaries.*') ? 'active' : '' }}">
                <i data-lucide="wallet"></i>
                <span>Quản lý lương</span>
            </a>
        </div>

        <!-- Quản lý tài khoản -->
        <div class="menu-section">
            <div class="menu-title">Quản lý tài khoản</div>
            <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="lock"></i>
                <span>Tài khoản người dùng</span>
            </a>
            <a href="{{ route('admin.users.roles') }}" class="menu-item {{ request()->routeIs('admin.users.roles') ? 'active' : '' }}">
                <i data-lucide="shield-check"></i>
                <span>Phân quyền</span>
            </a>
            <a href="{{ route('admin.users.activity') }}" class="menu-item {{ request()->routeIs('admin.users.activity') ? 'active' : '' }}">
                <i data-lucide="eye"></i>
                <span>Nhật ký hoạt động</span>
            </a>
        </div>

        <!-- Khác -->
        <div class="menu-section">
            <div class="menu-title">Khác</div>
            <a href="{{ route('admin.leave-requests.index') }}" class="menu-item {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>Đơn xin nghỉ</span>
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