@extends('layouts.user')

@section('title', 'Profile User')

@section('content')
    <div class="card">
        <h1>Profile</h1>
        <p>Nama: {{ auth()->user()->name }}</p>
        <p>Email: {{ auth()->user()->email }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
    </div>
@endsection
