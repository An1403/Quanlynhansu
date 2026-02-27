<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - HRMS</title>
    
    <!-- CSS Variables (Load đầu tiên) -->
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    
    <!-- CSS Reset & Base -->
    <link rel="stylesheet" href="{{ asset('css/shared/reset.css') }}">
    
    <!-- Shared Components CSS -->
    <link rel="stylesheet" href="{{ asset('css/shared/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/footer.css') }}">
    
    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">  
    
    <!-- Page Specific CSS -->
    @stack('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .content {
            padding: 30px;
            flex: 1;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error,
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .content {
                padding: 15px;
            }
        }

        /* Loading Spinner */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading.show {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar (sẽ được include bởi layout con) -->
    @yield('sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header chung -->
        @include('components.shared.header', [
            'title' => $pageTitle ?? 'Dashboard',
            'breadcrumb' => $breadcrumb ?? null,
            'notificationCount' => $notificationCount ?? 0
        ])

        <!-- Content -->
        <div class="content">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-error">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Warning Message -->
            @if(session('warning'))
                <div class="alert alert-warning">
                    <span>⚠️</span>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Info Message -->
            @if(session('info'))
                <div class="alert alert-info">
                    <span>ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <span>❌</span>
                    <div>
                        <strong>Có lỗi xảy ra:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Footer chung -->
        @include('components.shared.footer')
    </div>

    <!-- Loading Spinner -->
    <div class="loading" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <script>
        // Auto hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        function showLoading() {
            document.getElementById('loadingSpinner').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.remove('show');
        }

        function confirmDelete(message = 'Bạn có chắc chắn muốn xóa?') {
            return confirm(message);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    <!-- Shared Helper Functions -->
    <script src="{{ asset('js/shared/helpers.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')

    @yield('scripts')
</body>
</html>