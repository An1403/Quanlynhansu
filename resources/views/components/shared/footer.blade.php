<div class="footer">
    <div class="footer-left">
        <div class="footer-logo">
            <i class="fa-solid fa-helmet-safety"></i>
        </div>
        <div class="footer-text">
            <strong>HRMS - Hệ Thống Quản Lý Nhân Sự</strong>
            <span>© {{ date('Y') }} Construction Company. All rights reserved.</span>
        </div>
    </div>
    
    <div class="footer-right">
        <div class="footer-version">
            Version 1.0.0 | Laravel {{ app()->version() }} | PHP {{ PHP_VERSION }}
        </div>
        <div class="footer-links">
            <a href="#" onclick="showHelp(); return false;">
                <i class="fa-solid fa-book-open"></i>
                <span>Hướng dẫn</span>
            </a>
            <a href="#" onclick="showSupport(); return false;">
                <i class="fa-solid fa-comments"></i>
                <span>Hỗ trợ</span>
            </a>
            <a href="#" onclick="showAbout(); return false;">
                <i class="fa-solid fa-circle-info"></i>
                <span>Về chúng tôi</span>
            </a>
        </div>
    </div>
</div>

<script>
    function showHelp() {
        alert('📖 HƯỚNG DẪN SỬ DỤNG\n\n' +
              '1. Sử dụng menu bên trái để điều hướng\n' +
              '2. Click vào biểu tượng chuông để xem thông báo\n' +
              '3. Click vào avatar để xem thông tin cá nhân\n\n' +
              'Tài liệu chi tiết đang được phát triển!');
    }

    function showSupport() {
        alert('💬 HỖ TRỢ KỸ THUẬT\n\n' +
              'Liên hệ chúng tôi:\n' +
              '📧 Email: support@company.com\n' +
              '📞 Hotline: 1900-xxxx\n' +
              '🕐 Thời gian: 8:00 - 17:00 (T2-T6)\n\n' +
              'Chúng tôi sẽ phản hồi trong vòng 24h!');
    }

    function showAbout() {
        alert('ℹ️ VỀ HỆ THỐNG\n\n' +
              'HRMS - Human Resource Management System\n' +
              'Hệ thống quản lý nhân sự xây dựng\n\n' +
              '📌 Version: 1.0.0\n' +
              '🏢 Công ty: Construction Company\n' +
              '🔧 Framework: Laravel ' + '{{ app()->version() }}' + '\n' +
              '💻 PHP: ' + '{{ PHP_VERSION }}' + '\n\n' +
              '© {{ date("Y") }} All rights reserved.');
    }
</script>
