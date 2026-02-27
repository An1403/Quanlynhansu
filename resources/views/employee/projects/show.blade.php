@extends('layouts.employee')

@section('title', 'Chi tiết dự án - ' . $project->name)

@php
    $pageTitle = 'Chi tiết dự án';
    $breadcrumb = '<a href="' . route('employee.dashboard') . '">Home</a> / <a href="' . route('employee.projects.index') . '">Dự án</a> / ' . $project->name;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            color: #3b82f6;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #f3f4f6;
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 i {
            color: #3b82f6;
        }

        .card-body {
            padding: 25px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }

        .description {
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 20px;
        }

        .progress-section {
            margin-top: 20px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .progress-label-text {
            font-weight: 500;
            color: #374151;
        }

        .progress-percentage {
            font-weight: 600;
            color: #3b82f6;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .team-member {
            padding: 15px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            text-align: center;
            transition: all 0.2s;
        }

        .team-member:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.1);
        }

        .member-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            margin: 0 auto 10px;
        }

        .member-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .member-role {
            font-size: 12px;
            color: #6b7280;
        }

        .back-btn {
            padding: 8px 14px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        .empty-message {
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-folder-open"></i>
        {{ $project->name }}
    </h1>
    <a href="{{ route('employee.projects.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Quay lại
    </a>
</div>

<!-- Project Info -->
<div class="card">
    <div class="card-header">
        <h3>
            <i class="fas fa-info-circle"></i>
            Thông tin dự án
        </h3>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 15px;">
            <span class="badge badge-{{ str_replace(' ', '\\ ', $project->status) }}">
                {{ $project->status_label }}
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Quản lý dự án</div>
                <div class="info-value">{{ $project->manager ? $project->manager->full_name : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Ngày bắt đầu</div>
                <div class="info-value">{{ $project->start_date ? $project->start_date->format('d/m/Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Ngày kết thúc</div>
                <div class="info-value">{{ $project->end_date ? $project->end_date->format('d/m/Y') : 'N/A' }}</div>
            </div>
        </div>

        @if($project->description)
            <div class="description">
                {{ $project->description }}
            </div>
        @endif

        @if($project->progress)
            <div class="progress-section">
                <div class="progress-label">
                    <span class="progress-label-text">Tiến độ dự án</span>
                    <span class="progress-percentage">{{ $project->progress }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $project->progress }}%"></div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Team Members -->
@if($project->team_members && $project->team_members->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fas fa-users"></i>
                Thành viên dự án ({{ $project->team_members->count() }})
            </h3>
        </div>
        <div class="card-body">
            <div class="team-grid">
                @foreach($project->team_members as $member)
                    <div class="team-member">
                        <div class="member-avatar">
                            {{ substr($member->full_name, 0, 1) }}
                        </div>
                        <div class="member-name">{{ $member->full_name }}</div>
                        <div class="member-role">
                            {{ $member->position ? $member->position->name : 'Nhân viên' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@endsection