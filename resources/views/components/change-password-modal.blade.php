<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-key"></i> Đổi Mật Khẩu</h3>
            <button class="modal-close" onclick="closeChangePasswordModal()">&times;</button>
        </div>

        <form id="changePasswordForm" method="POST" action="{{ route('change-password') }}">
            @csrf

            <div class="form-group">
                <label for="currentPassword">
                    <i class="fa-solid fa-lock"></i> Mật khẩu hiện tại
                    <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="currentPassword" 
                    name="current_password" 
                    class="form-control" 
                    required
                    placeholder="Nhập mật khẩu hiện tại">
                <span class="error-message" id="currentPasswordError"></span>
            </div>

            <div class="form-group">
                <label for="newPassword">
                    <i class="fa-solid fa-key"></i> Mật khẩu mới
                    <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="newPassword" 
                    name="password" 
                    class="form-control" 
                    required
                    placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                <span class="error-message" id="newPasswordError"></span>
            </div>

            <div class="form-group">
                <label for="confirmPassword">
                    <i class="fa-solid fa-key"></i> Xác nhận mật khẩu mới
                    <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="confirmPassword" 
                    name="password_confirmation" 
                    class="form-control" 
                    required
                    placeholder="Nhập lại mật khẩu mới">
                <span class="error-message" id="confirmPasswordError"></span>
            </div>

            <div id="successMessage" class="success-message" style="display: none;">
                <i class="fa-solid fa-check-circle"></i> <span id="successText"></span>
            </div>

            <div id="errorMessage" class="error-message-box" style="display: none;">
                <i class="fa-solid fa-exclamation-circle"></i> <span id="errorText"></span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">
                    <i class="fa-solid fa-times"></i> Hủy bỏ
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Cập nhật mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Styles -->
<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 15px;
    }

    .modal-header h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #9ca3af;
        transition: color 0.3s;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: #374151;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }

    .required {
        color: #ef4444;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.error {
        border-color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .success-message {
        background: #dcfce7;
        color: #166534;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #10b981;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .error-message-box {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #ef4444;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 25px;
        border-top: 1px solid #e5e7eb;
        padding-top: 15px;
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-primary:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    @media (max-width: 480px) {
        .modal-content {
            width: 95%;
            padding: 20px;
        }

        .modal-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- JavaScript Functions -->
<script>
    function openChangePasswordModal() {
        document.getElementById('changePasswordModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('changePasswordForm').reset();
        clearMessages();
    }

    function clearMessages() {
        document.getElementById('successMessage').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('currentPasswordError').textContent = '';
        document.getElementById('newPasswordError').textContent = '';
        document.getElementById('confirmPasswordError').textContent = '';
    }

    // Handle form submission
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearMessages();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Client-side validation
        let hasError = false;

        if (!currentPassword) {
            document.getElementById('currentPasswordError').textContent = 'Vui lòng nhập mật khẩu hiện tại';
            hasError = true;
        }

        if (!newPassword || newPassword.length < 6) {
            document.getElementById('newPasswordError').textContent = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            hasError = true;
        }

        if (newPassword !== confirmPassword) {
            document.getElementById('confirmPasswordError').textContent = 'Mật khẩu xác nhận không khớp';
            hasError = true;
        }

        if (currentPassword === newPassword) {
            document.getElementById('newPasswordError').textContent = 'Mật khẩu mới không được giống mật khẩu cũ';
            hasError = true;
        }

        if (hasError) return;

        // Submit form
        this.submit();
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('changePasswordModal');
        if (event.target === modal) {
            closeChangePasswordModal();
        }
    });

    // Keyboard escape key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeChangePasswordModal();
        }
    });
</script>