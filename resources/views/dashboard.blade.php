@extends('layouts.app')

@section('title', 'Dashboard - CAPDEVhub')
@section('page-title', 'Dashboard')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
    <p class="text-gray-600">Welcome to CAPDEVhub! Your account has been successfully created.</p>
    
    <div class="mt-6 p-4 bg-[#0a7ca1] bg-opacity-10 rounded-lg">
        <h3 class="font-semibold text-gray-900 mb-2">Your Account Information</h3>
        <div class="space-y-2 text-sm">
            <p><strong>Name:</strong> {{ auth()->user()->full_name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Role:</strong> <span class="uppercase">{{ auth()->user()->role }}</span></p>
            <p><strong>Office:</strong> {{ auth()->user()->office }}</p>
            <p><strong>Position:</strong> {{ auth()->user()->position }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a 
            href="{{ route('profile.show') }}" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#013141] hover:bg-[#0a7ca1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0a7ca1] transition"
        >
            View My Information
        </a>
    </div>
</div>
@endsection
