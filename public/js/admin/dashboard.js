/**
 * ========================================
 * Admin Dashboard JavaScript
 * ========================================
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Admin Dashboard initialized');
    
    // Initialize dashboard
    initDashboard();
    
    // Initialize chart animations
    initChartAnimations();
    
    // Initialize stat cards hover effects
    initStatCards();
});

// Initialize Dashboard
function initDashboard() {
    // Add any dashboard initialization code here
    console.log('Dashboard initialized at:', new Date().toLocaleString('vi-VN'));
}

// Chart Animations
function initChartAnimations() {
    const chartBars = document.querySelectorAll('.chart-bar');
    
    chartBars.forEach((bar, index) => {
        // Animate bars on load
        setTimeout(() => {
            bar.style.opacity = '0';
            bar.style.transform = 'scaleY(0)';
            bar.style.transformOrigin = 'bottom';
            bar.style.transition = 'all 0.5s ease-out';
            
            setTimeout(() => {
                bar.style.opacity = '1';
                bar.style.transform = 'scaleY(1)';
            }, 50);
        }, index * 100);
        
        // Add hover tooltip
        bar.addEventListener('mouseenter', function() {
            const label = this.nextElementSibling;
            if (label) {
                label.style.fontWeight = 'bold';
                label.style.color = '#667eea';
            }
        });
        
        bar.addEventListener('mouseleave', function() {
            const label = this.nextElementSibling;
            if (label) {
                label.style.fontWeight = 'normal';
                label.style.color = '#666';
            }
        });
    });
}

// Stat Cards Hover Effects
function initStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        // Animate cards on load
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
        
        // Add click effect
        card.addEventListener('click', function() {
            const statValue = this.querySelector('.stat-value');
            if (statValue) {
                statValue.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    statValue.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });
}

// Refresh Dashboard Data
function refreshDashboard() {
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        showToast('Dashboard đã được làm mới!', 'success');
    }, 1000);
}

// Export Dashboard Data
function exportDashboard(format = 'pdf') {
    showLoading();
    
    console.log(`Exporting dashboard as ${format}...`);
    
    setTimeout(() => {
        hideLoading();
        showToast(`Xuất báo cáo ${format.toUpperCase()} thành công!`, 'success');
    }, 1500);
}

// Filter Dashboard by Date Range
function filterByDateRange(startDate, endDate) {
    console.log(`Filtering dashboard from ${startDate} to ${endDate}`);
    showLoading();
    
    setTimeout(() => {
        hideLoading();
        showToast('Đã lọc dữ liệu!', 'info');
    }, 800);
}

// Quick Actions
const quickActions = {
    addEmployee: function() {
        window.location.href = '/admin/employees/create';
    },
    
    viewAttendance: function() {
        window.location.href = '/admin/attendances';
    },
    
    calculateSalary: function() {
        window.location.href = '/admin/salaries/create';
    },
    
    viewReports: function() {
        window.location.href = '/admin/reports';
    }
};

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R: Refresh Dashboard
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshDashboard();
    }
    
    // Ctrl/Cmd + E: Export Dashboard
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportDashboard('pdf');
    }
});

// Update Clock (if exists)
function updateClock() {
    const clockElement = document.getElementById('dashboard-clock');
    if (clockElement) {
        const now = new Date();
        clockElement.textContent = now.toLocaleTimeString('vi-VN');
    }
}

// Update clock every second
setInterval(updateClock, 1000);

console.log('📊 Dashboard JS loaded successfully');