@extends('layouts.app')

@section('title', 'Users - CAPDEVhub')
@section('page-title', 'Users')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6">
    @livewire('users-table')
</div>
@endsection
