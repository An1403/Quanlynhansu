@extends('layouts.employee')

@section('title', 'Chỉnh sửa Chấm công')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employees.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit"></i> Chỉnh sửa Chấm công</h1>
    <div class="page-actions">
        <a href="{{ route('employee.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>
</div>

<form action="{{ route('employee.attendance.update', $attendance->id) }}" method="POST">
    @csrf
    @method('PUT')
    
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
                    value="{{ old('date', $attendance->date->format('Y-m-d')) }}" 
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
                    value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}"
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
                    value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}"
                    onchange="calculateWorkingHours()">
                @error('check_out')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Số giờ làm -->
            <div class="form-group">
                <label for="working_hours">
                    <i class="fas fa-hourglass-half"></i> Số giờ làm
                </label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="working_hours" 
                    name="working_hours" 
                    value="{{ old('working_hours', $attendance->working_hours ?? 0) }}"
                    step="0.5"
                    min="0">
                <small style="color: #6b7280; font-size: 12px;">
                    <i class="fas fa-lightbulb"></i> Tự động tính từ giờ vào và giờ ra
                </small>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-flag"></i> Trạng thái 
                    <span class="required">*</span>
                </label>
                <select class="form-control @error('status') error @enderror" id="status" name="status" required>
                    <option value="Present" {{ old('status', $attendance->status) == 'Present' ? 'selected' : '' }}>Có mặt</option>
                    <option value="Leave" {{ old('status', $attendance->status) == 'Leave' ? 'selected' : '' }}>Xin phép</option>
                    <option value="Absent" {{ old('status', $attendance->status) == 'Absent' ? 'selected' : '' }}>Vắng mặt</option>
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
                        <option value="{{ $project->id }}" {{ old('project_id', $attendance->project_id) == $project->id ? 'selected' : '' }}>
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
                    placeholder="Nhập ghi chú (tùy chọn)">{{ old('notes', $attendance->notes) }}</textarea>
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
            <span>Cập nhật</span>
        </button>
    </div>
</form>

@push('scripts')
<script>
    function calculateWorkingHours() {
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const workingHoursInput = document.getElementById('working_hours');
        
        if (checkIn && checkOut) {
            const checkInTime = new Date(`2000-01-01 ${checkIn}`);
            const checkOutTime = new Date(`2000-01-01 ${checkOut}`);
            
            if (checkOutTime > checkInTime) {
                const diffMs = checkOutTime - checkInTime;
                const diffHours = diffMs / (1000 * 60 * 60);
                workingHoursInput.value = diffHours.toFixed(1);
            }
        }
    }
</script>
@endpush
@endsection