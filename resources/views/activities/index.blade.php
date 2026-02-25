@extends('layouts.app')

@section('title', 'Activities - CAPDEVhub')
@section('page-title', 'Activities')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6">
    @livewire('activities-table')
</div>
@endsection

