<style>
    /* Accountant specific gradient - Green theme */
    .sidebar.accountant {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
</style>

<div class="sidebar accountant" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">💰</div>
        <h3>KẾ TOÁN</h3>
        <p>Quản Lý Tài Chính</p>
    </div>

    <div class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-section">
            <div class="menu-title">Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i>📊</i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Quản lý lương -->
        <div class="menu-section">
            <div class="menu-title">Quản lý lương</div>
            <a href="{{ route('admin.salaries.index') }}" class="menu-item {{ request()->routeIs('admin.salaries.*') ? 'active' : '' }}">
                <i>💵</i>
                <span>Bảng lương</span>
            </a>
            <a href="{{ route('admin.salaries.create') }}" class="menu-item">
                <i>🧮</i>
                <span>Tính lương</span>
            </a>
        </div>

        <!-- Chấm công -->
        <div class="menu-section">
            <div class="menu-title">Chấm công</div>
            <a href="{{ route('admin.attendances.index') }}" class="menu-item {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                <i>⏰</i>
                <span>Xem chấm công</span>
            </a>
        </div>

        <!-- Nhân sự -->
        <div class="menu-section">
            <div class="menu-title">Nhân sự</div>
            <a href="{{ route('admin.employees.index') }}" class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i>👥</i>
                <span>Danh sách nhân viên</span>
            </a>
        </div>

        <!-- Báo cáo -->
        <div class="menu-section">
            <div class="menu-title">Báo cáo</div>
            <a href="#" class="menu-item">
                <i>📈</i>
                <span>Báo cáo tháng</span>
            </a>
            <a href="#" class="menu-item">
                <i>📊</i>
                <span>Báo cáo năm</span>
            </a>
            <a href="#" class="menu-item">
                <i>📄</i>
                <span>Xuất báo cáo</span>
            </a>
        </div>
    </div>
</div>

<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<script>
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