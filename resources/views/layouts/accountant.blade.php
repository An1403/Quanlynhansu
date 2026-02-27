@extends('layouts.app')

@section('sidebar')
    @include('components.sidebars.accountant')
@endsection
@include('components.change-password-modal')
@include('components.settings-modal')