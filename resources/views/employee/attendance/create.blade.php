@extends('layouts.employee')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Thêm Chấm công Mới</h1>
    <div class="page-actions">
        <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('employee.attendance.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Thông tin Chấm công
        </h3>
        
        <div class="form-grid">
            <!-- Ngày -->
            <div class="form-group">
                <label for="date">
                    <i class="fas fa-calendar-alt"></i> Ngày 
                    <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    class="form-control @error('date') error @enderror" 
                    id="date" 
                    name="date" 
                    value="{{ old('date', date('Y-m-d')) }}"
                    required>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Giờ vào -->
            <div class="form-group">
                <label for="check_in">
                    <i class="fas fa-sign-in-alt"></i> Giờ vào
                </label>
                <input 
                    type="time" 
                    class="form-control @error('check_in') error @enderror" 
                    id="check_in" 
                    name="check_in" 
                    value="{{ old('check_in') }}"
                    onchange="calculateWorkingHours()">
                @error('check_in')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Giờ ra -->
            <div class="form-group">
                <label for="check_out">
                    <i class="fas fa-sign-out-alt"></i> Giờ ra
                </label>
                <input 
                    type="time" 
                    class="form-control @error('check_out') error @enderror" 
                    id="check_out" 
                    name="check_out" 
                    value="{{ old('check_out') }}"
                    onchange="calculateWorkingHours()">
                @error('check_out')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Số giờ làm -->
            <div class="form-group">
                <label for="working_hours_display">
                    <i class="fas fa-hourglass-half"></i> Số giờ làm
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="working_hours_display" 
                    value="0.0 giờ"
                    readonly
                    style="background-color: #f3f4f6; cursor: not-allowed;">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính từ giờ vào/ra (đã trừ 1.5h nghỉ trưa)
                </small>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-flag"></i> Trạng thái 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status" required>
                    <option value="">-- Chọn trạng thái --</option>
                    <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Có mặt</option>
                    <option value="Leave" {{ old('status') == 'Leave' ? 'selected' : '' }}>Xin phép</option>
                    <option value="Absent" {{ old('status') == 'Absent' ? 'selected' : '' }}>Vắng mặt</option>
                </select>
                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Dự án -->
            <div class="form-group">
                <label for="project_id">
                    <i class="fas fa-project-diagram"></i> Dự án
                </label>
                <select class="form-control" id="project_id" name="project_id">
                    <option value="">-- Không có dự án --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ghi chú -->
            <div class="form-group full-width">
                <label for="notes">
                    <i class="fas fa-sticky-note"></i> Ghi chú
                </label>
                <textarea 
                    class="form-control @error('notes') error @enderror" 
                    id="notes" 
                    name="notes" 
                    rows="3"
                    placeholder="Nhập ghi chú (tùy chọn)">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Hủy bỏ</span>
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            <span>Lưu thông tin</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ✅ Tính số giờ làm TRỪ 1.5 TIẾNG NGHỈ TRƯA
     */
    function calculateWorkingHours() {
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const displayInput = document.getElementById('working_hours_display');
        
        if (checkIn && checkOut) {
            const checkInTime = new Date(`2000-01-01 ${checkIn}:00`);
            const checkOutTime = new Date(`2000-01-01 ${checkOut}:00`);
            
            if (checkOutTime > checkInTime) {
                // Tính tổng số phút
                const diffMs = checkOutTime - checkInTime;
                const diffMinutes = diffMs / (1000 * 60);
                
                // TRỪ 90 PHÚT NGHỈ TRƯA (1.5 tiếng)
                const workingMinutes = diffMinutes - 90;
                
                if (workingMinutes <= 0) {
                    displayInput.value = '0.0 giờ';
                    displayInput.style.color = '#ef4444';
                } else {
                    const workingHours = (workingMinutes / 60).toFixed(1);
                    displayInput.value = workingHours + ' giờ';
                    displayInput.style.color = '#059669';
                }
                
                // Log chi tiết
                console.log(`
                    ⏰ Tính toán giờ làm:
                    - Giờ vào: ${checkIn}
                    - Giờ ra: ${checkOut}
                    - Tổng thời gian: ${Math.floor(diffMinutes / 60)}h ${Math.round(diffMinutes % 60)}m
                    - Trừ nghỉ trưa: 1.5h (90 phút)
                    - Giờ làm thực tế: ${workingMinutes > 0 ? (workingMinutes / 60).toFixed(1) : 0}h
                `);
            } else {
                displayInput.value = '0.0 giờ';
                displayInput.style.color = '#6b7280';
            }
        } else {
            displayInput.value = '0.0 giờ';
            displayInput.style.color = '#6b7280';
        }
    }

    // Tự động tính khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        calculateWorkingHours();
    });

    console.log('✅ Attendance form loaded with lunch break calculation');
</script>
@endpush
@endsection