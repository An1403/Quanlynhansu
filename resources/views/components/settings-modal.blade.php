<!-- Settings Modal -->
<div id="settingsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-gear"></i> Cài Đặt</h3>
            <button class="modal-close" onclick="closeSettingsModal()">&times;</button>
        </div>

        <div class="settings-tabs">
            <button class="settings-tab-btn active" onclick="switchSettingsTab('general')">
                <i class="fa-solid fa-sliders"></i> Chung
            </button>
            <button class="settings-tab-btn" onclick="switchSettingsTab('notification')">
                <i class="fa-solid fa-bell"></i> Thông báo
            </button>
            <button class="settings-tab-btn" onclick="switchSettingsTab('privacy')">
                <i class="fa-solid fa-shield"></i> Bảo mật
            </button>
        </div>

        <!-- General Settings -->
        <div id="generalTab" class="settings-tab-content active">
            <form id="generalSettingsForm" method="POST" action="{{ route('settings.update', 'general') }}">
                @csrf
                @method('PUT')

                <div class="settings-group">
                    <h4>Cài đặt chung</h4>

                    <div class="form-group">
                        <label for="language">
                            <i class="fa-solid fa-globe"></i> Ngôn ngữ
                        </label>
                        <select id="language" name="language" class="form-control">
                            <option value="vi" {{ Auth::user()->getSetting('language', 'vi') === 'vi' ? 'selected' : '' }}>
                                Tiếng Việt
                            </option>
                            <option value="en" {{ Auth::user()->getSetting('language', 'vi') === 'en' ? 'selected' : '' }}>
                                English
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="theme">
                            <i class="fa-solid fa-palette"></i> Giao diện
                        </label>
                        <select id="theme" name="theme" class="form-control">
                            <option value="light" {{ Auth::user()->getSetting('theme', 'light') === 'light' ? 'selected' : '' }}>
                                Sáng
                            </option>
                            <option value="dark" {{ Auth::user()->getSetting('theme', 'light') === 'dark' ? 'selected' : '' }}>
                                Tối
                            </option>
                            <option value="auto" {{ Auth::user()->getSetting('theme', 'light') === 'auto' ? 'selected' : '' }}>
                                Tự động
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dateFormat">
                            <i class="fa-solid fa-calendar"></i> Định dạng ngày tháng
                        </label>
                        <select id="dateFormat" name="date_format" class="form-control">
                            <option value="d/m/Y" {{ Auth::user()->getSetting('date_format', 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>
                                Ngày/Tháng/Năm (01/01/2025)
                            </option>
                            <option value="m/d/Y" {{ Auth::user()->getSetting('date_format', 'd/m/Y') === 'm/d/Y' ? 'selected' : '' }}>
                                Tháng/Ngày/Năm (01/01/2025)
                            </option>
                            <option value="Y-m-d" {{ Auth::user()->getSetting('date_format', 'd/m/Y') === 'Y-m-d' ? 'selected' : '' }}>
                                Năm-Tháng-Ngày (2025-01-01)
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="itemsPerPage">
                            <i class="fa-solid fa-list"></i> Số mục trên mỗi trang
                        </label>
                        <select id="itemsPerPage" name="items_per_page" class="form-control">
                            <option value="10" {{ Auth::user()->getSetting('items_per_page', '15') === '10' ? 'selected' : '' }}>10</option>
                            <option value="15" {{ Auth::user()->getSetting('items_per_page', '15') === '15' ? 'selected' : '' }}>15</option>
                            <option value="25" {{ Auth::user()->getSetting('items_per_page', '15') === '25' ? 'selected' : '' }}>25</option>
                            <option value="50" {{ Auth::user()->getSetting('items_per_page', '15') === '50' ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeSettingsModal()">
                        <i class="fa-solid fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Settings -->
        <div id="notificationTab" class="settings-tab-content">
            <form id="notificationSettingsForm" method="POST" action="{{ route('settings.update', 'notification') }}">
                @csrf
                @method('PUT')

                <div class="settings-group">
                    <h4>Cài đặt thông báo</h4>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="emailNotification" name="email_notification" 
                            {{ Auth::user()->getSetting('email_notification', true) ? 'checked' : '' }}>
                        <label for="emailNotification">
                            <i class="fa-solid fa-envelope"></i> Nhận thông báo qua email
                        </label>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="leaveNotification" name="leave_notification" 
                            {{ Auth::user()->getSetting('leave_notification', true) ? 'checked' : '' }}>
                        <label for="leaveNotification">
                            <i class="fa-solid fa-calendar-xmark"></i> Thông báo về đơn xin nghỉ
                        </label>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="attendanceNotification" name="attendance_notification" 
                            {{ Auth::user()->getSetting('attendance_notification', true) ? 'checked' : '' }}>
                        <label for="attendanceNotification">
                            <i class="fa-solid fa-clock"></i> Thông báo về chấm công
                        </label>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="salaryNotification" name="salary_notification" 
                            {{ Auth::user()->getSetting('salary_notification', true) ? 'checked' : '' }}>
                        <label for="salaryNotification">
                            <i class="fa-solid fa-wallet"></i> Thông báo về lương
                        </label>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="systemNotification" name="system_notification" 
                            {{ Auth::user()->getSetting('system_notification', true) ? 'checked' : '' }}>
                        <label for="systemNotification">
                            <i class="fa-solid fa-bell"></i> Thông báo hệ thống
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeSettingsModal()">
                        <i class="fa-solid fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>

        <!-- Privacy Settings -->
        <div id="privacyTab" class="settings-tab-content">
            <form id="privacySettingsForm" method="POST" action="{{ route('settings.update', 'privacy') }}">
                @csrf
                @method('PUT')

                <div class="settings-group">
                    <h4>Cài đặt bảo mật</h4>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="twoFactor" name="two_factor_enabled" 
                            {{ Auth::user()->getSetting('two_factor_enabled', false) ? 'checked' : '' }}>
                        <label for="twoFactor">
                            <i class="fa-solid fa-shield-check"></i> Xác thực hai yếu tố
                        </label>
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            Bảo vệ tài khoản bằng mã xác thực qua điện thoại
                        </small>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="loginAlert" name="login_alert" 
                            {{ Auth::user()->getSetting('login_alert', true) ? 'checked' : '' }}>
                        <label for="loginAlert">
                            <i class="fa-solid fa-exclamation-triangle"></i> Cảnh báo đăng nhập
                        </label>
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            Nhận thông báo khi có đăng nhập từ thiết bị mới
                        </small>
                    </div>

                    <div class="form-group checkbox">
                        <input type="checkbox" id="activityLog" name="activity_log" 
                            {{ Auth::user()->getSetting('activity_log', true) ? 'checked' : '' }}>
                        <label for="activityLog">
                            <i class="fa-solid fa-history"></i> Nhật ký hoạt động
                        </label>
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            Ghi lại tất cả hoạt động của bạn
                        </small>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-calendar"></i> Thời gian hết phiên làm việc
                        </label>
                        <select name="session_timeout" class="form-control">
                            <option value="30" {{ Auth::user()->getSetting('session_timeout', '60') === '30' ? 'selected' : '' }}>
                                30 phút
                            </option>
                            <option value="60" {{ Auth::user()->getSetting('session_timeout', '60') === '60' ? 'selected' : '' }}>
                                1 giờ
                            </option>
                            <option value="120" {{ Auth::user()->getSetting('session_timeout', '60') === '120' ? 'selected' : '' }}>
                                2 giờ
                            </option>
                            <option value="480" {{ Auth::user()->getSetting('session_timeout', '60') === '480' ? 'selected' : '' }}>
                                8 giờ
                            </option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeSettingsModal()">
                        <i class="fa-solid fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .settings-tabs {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .settings-tab-btn {
        background: none;
        border: none;
        padding: 12px 16px;
        cursor: pointer;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .settings-tab-btn:hover {
        color: #374151;
    }

    .settings-tab-btn.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }

    .settings-tab-content {
        display: none;
        animation: fadeIn 0.3s ease-in;
    }

    .settings-tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .settings-group {
        margin-bottom: 25px;
    }

    .settings-group h4 {
        margin: 0 0 15px 0;
        color: #111827;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 10px;
    }

    .form-group.checkbox {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .form-group.checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    .form-group.checkbox label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #374151;
        font-size: 14px;
    }
</style>

<!-- JavaScript -->
<script>
    function openSettingsModal() {
        document.getElementById('settingsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeSettingsModal() {
        document.getElementById('settingsModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function switchSettingsTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.settings-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName + 'Tab').classList.add('active');

        // Add active class to clicked button
        event.target.closest('.settings-tab-btn').classList.add('active');
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('settingsModal');
        if (event.target === modal) {
            closeSettingsModal();
        }
    });

    // Keyboard escape key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSettingsModal();
        }
    });

    // Handle form submissions
    document.getElementById('generalSettingsForm')?.addEventListener('submit', handleSettingsSubmit);
    document.getElementById('notificationSettingsForm')?.addEventListener('submit', handleSettingsSubmit);
    document.getElementById('privacySettingsForm')?.addEventListener('submit', handleSettingsSubmit);

    function handleSettingsSubmit(e) {
        e.preventDefault();
        this.submit();
    }
</script>