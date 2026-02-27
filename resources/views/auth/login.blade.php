<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng Nhập - HRMS</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS Variables -->
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    
    <!-- CSS Reset -->
    <link rel="stylesheet" href="{{ asset('css/shared/reset.css') }}">
    
    <!-- Login Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-left">
            <div class="logo-section">
                <div class="logo">
                    <i class="fa-solid fa-helmet-safety"></i>
                </div>
                <h1>CTy Xây Dựng Thành An</h1>
                <p>Hệ Thống Quản Lý Nhân Sự</p>
            </div>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Đăng Nhập</h2>
                <p>Chào mừng bạn trở lại! Vui lòng đăng nhập để tiếp tục.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Nhập tên đăng nhập" 
                            value="{{ old('username') }}"
                            class="@error('username') error @enderror"
                            required>
                    </div>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Nhập mật khẩu"
                            class="@error('password') error @enderror"
                            required>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="forgot-password">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tự động ẩn alert sau 5 giây
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Animation fadeOut
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-10px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
