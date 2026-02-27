/**
 * ========================================
 * Shared Helper Functions
 * ========================================
 */

// Get CSRF Token
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

// Show Loading Spinner
function showLoading() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.add('show');
    }
}

// Hide Loading Spinner
function hideLoading() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.remove('show');
    }
}

// Confirm Delete
function confirmDelete(message = 'Bạn có chắc chắn muốn xóa?') {
    return confirm(message);
}

// Show Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.innerHTML = `
        <span>${getIcon(type)}</span>
        <span>${message}</span>
    `;
    
    const content = document.querySelector('.content');
    if (content) {
        content.insertBefore(toast, content.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
}

// Get icon by type
function getIcon(type) {
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    return icons[type] || '📌';
}

// Format Date (DD/MM/YYYY)
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Format Number with thousand separator
function formatNumber(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

// Format Currency (VND)
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Debounce function
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Copy to Clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Đã copy vào clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy:', err);
            showToast('Không thể copy!', 'error');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showToast('Đã copy vào clipboard!', 'success');
        } catch (err) {
            showToast('Không thể copy!', 'error');
        }
        document.body.removeChild(textArea);
    }
}

// Validate Email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate Phone (Vietnam)
function isValidPhone(phone) {
    const re = /^(0|\+84)[0-9]{9}$/;
    return re.test(phone);
}

// Auto hide alerts on page load
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Export functions for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        getCsrfToken,
        showLoading,
        hideLoading,
        confirmDelete,
        showToast,
        formatDate,
        formatNumber,
        formatCurrency,
        debounce,
        copyToClipboard,
        isValidEmail,
        isValidPhone
    };
}