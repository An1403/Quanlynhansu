<!-- Header -->
@php
    // Lấy thông tin employee từ user hiện tại
    $currentEmployee = DB::table('employees')
        ->where('user_id', Auth::id())
        ->first();
    
    // Fallback values
    $employeeName = $currentEmployee->full_name ?? Auth::user()->username ?? 'User';
    $employeeEmail = $currentEmployee->email ?? 'N/A';
    $employeePhoto = $currentEmployee->photo ?? null;
    $employeeCode = $currentEmployee->employee_code ?? 'N/A';
@endphp

<div class="header">
    <div class="header-left">
        <h2>{{ $title ?? 'Dashboard' }}</h2>
        @if(isset($breadcrumb))
            <div class="breadcrumb">
                {!! $breadcrumb !!}
            </div>
        @endif
    </div>
    
    <div class="header-right">
        <!-- Notifications -->
        <button class="notification-btn" onclick="showNotifications()" title="Thông báo">
            <i class="fa-solid fa-bell"></i>
            @if(($notificationCount ?? 0) > 0)
                <span class="notification-badge">{{ $notificationCount }}</span>
            @endif
        </button>

        <!-- User Dropdown -->
        <div class="user-dropdown">
            <div class="user-info" onclick="toggleUserMenu()">
                <!-- Avatar từ employees.photo hoặc chữ cái đầu -->
                @if($employeePhoto && file_exists(public_path('storage/' . $employeePhoto)))
                    <img src="{{ asset('storage/' . $employeePhoto) }}" 
                         alt="{{ $employeeName }}" 
                         class="user-avatar-img"
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                @else
                    <div class="user-avatar {{ Auth::user()->role ?? 'admin' }}">
                        {{ strtoupper(substr($employeeName, 0, 1)) }}
                    </div>
                @endif
                
                <div class="user-details">
                    <div class="user-name">{{ $employeeName }}</div>
                    <div class="user-role">
                        @switch(Auth::user()->role ?? 'admin')
                            @case('admin')
                                Quản trị viên
                                @break
                            @case('accountant')
                                Kế toán
                                @break
                            @case('employee')
                                Nhân viên
                                @break
                            @default
                                Người dùng
                        @endswitch
                    </div>
                </div>
                <i class="fa-solid fa-caret-down" style="font-size: 14px; color: #999;"></i>
            </div>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="userDropdown">
                <div class="dropdown-header">
                    <!-- Avatar lớn hơn trong dropdown -->
                    <div style="text-align: center; margin-bottom: 12px;">
                        @if($employeePhoto && file_exists(public_path('storage/' . $employeePhoto)))
                            <img src="{{ asset('storage/' . $employeePhoto) }}" 
                                 alt="{{ $employeeName }}" 
                                 style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb;">
                        @else
                            <div class="user-avatar {{ Auth::user()->role ?? 'admin' }}" 
                                 style="width: 60px; height: 60px; font-size: 24px; line-height: 60px; margin: 0 auto;">
                                {{ strtoupper(substr($employeeName, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="dropdown-header-name">{{ $employeeName }}</div>
                    <div class="dropdown-header-email">{{ $employeeEmail }}</div>
                    @if($currentEmployee)
                        <div style="font-size: 12px; color: #9ca3af; margin-top: 4px;">
                            <i class="fa-solid fa-id-badge"></i> {{ $employeeCode }}
                        </div>
                    @endif
                </div>

                <!-- Menu Items -->
                @if(Auth::user()->role === 'employee')
                    <a href="{{ route('employee.profile.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-user"></i>
                        <span>Hồ sơ cá nhân</span>
                    </a>
                @endif
                
                <a href="#" class="dropdown-item" onclick="openChangePasswordModal(); return false;">
                    <i class="fa-solid fa-key"></i>
                    <span>Đổi mật khẩu</span>
                </a>
                <a href="#" class="dropdown-item" onclick="openSettingsModal(); return false;">
                    <i class="fa-solid fa-gear"></i>
                    <span>Cài đặt</span>
                </a>
                
                <div class="dropdown-divider"></div>
                
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); confirmLogout();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Logout Form (Hidden) -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
/* Style cho avatar hình ảnh */
.user-avatar-img {
    border: 2px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-avatar-img:hover {
    border-color: #3b82f6;
}

/* Dropdown header styling */
.dropdown-header {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.dropdown-header-name {
    font-weight: 600;
    font-size: 16px;
    color: white;
    margin-bottom: 4px;
}

.dropdown-header-email {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
}
</style>

@php
    // Tạo object cho JavaScript
    $employeeInfoData = [
        'name' => $employeeName,
        'email' => $employeeEmail,
        'code' => $employeeCode,
        'role' => Auth::user()->role ?? 'user'
    ];
@endphp

<script>
    // ✅ Truyền biến PHP vào JavaScript an toàn
    const notificationCount = {{ $notificationCount ?? 0 }};
    const employeeInfo = {
        name: "{{ $employeeName }}",
        email: "{{ $employeeEmail }}",
        code: "{{ $employeeCode }}",
        role: "{{ Auth::user()->role ?? 'user' }}"
    };

    function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    function showNotifications() {
        alert('🔔 Thông báo\n\nBạn có ' + notificationCount + ' thông báo mới!\n\nChức năng này đang được phát triển.');
    }

    function confirmLogout() {
        if (confirm('👋 Tạm biệt ' + employeeInfo.name + '!\n\nBạn có chắc chắn muốn đăng xuất?')) {
            document.getElementById('logout-form').submit();
        }
    }

    // Đóng dropdown khi click bên ngoài
    document.addEventListener('click', function(event) {
        const userDropdown = document.querySelector('.user-dropdown');
        const dropdown = document.getElementById('userDropdown');
        
        if (userDropdown && !userDropdown.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Log thông tin employee để debug
    console.log('👤 Employee Info:', employeeInfo);
</script>