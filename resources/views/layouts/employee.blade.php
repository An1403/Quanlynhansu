@extends('layouts.app')

@section('sidebar')
    @include('components.sidebars.employee')
@endsection
@include('components.change-password-modal')
@include('components.settings-modal')