@extends('layouts.app')

@section('sidebar')
    @include('components.sidebars.admin')
@endsection
@include('components.change-password-modal')
@include('components.settings-modal')