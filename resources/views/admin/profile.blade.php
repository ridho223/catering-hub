@extends('layouts.admin')
@section('title','Profile')
@section('content')

<div class="profile-page">

    <div class="profile-card">

        <div class="profile-header">
            
           @if ($user->avatar)
                <img src="{{asset('storage/'. $user->avatar)}}" class="profile-avatar profile-avatar-image" alt="{{$user->name}}">
           @else
                <div class="profile-avatar">
                    {{strtoupper(substr($user->name, 0,2))}}
                </div>
           @endif


            <div class="profile-info">
                <h3>{{$user->name}}</h3>

                <p>{{$user->email}}</p>

                <span class="profile-role">
                    <i class="bi bi-shield-check"></i>
                    {{$user->level}}
                </span>
            </div>
            
        </div>

        <hr>

        <div class="profile-details">

            <div class="profile-item">
                <div class="profile-icon">
                    <i class="bi bi-person"></i>
                </div>

                <div>
                    <small>Nama</small>
                    <strong>{{$user->name}}</strong>
                </div>
            </div>

            <div class="profile-item">
                <div class="profile-icon">
                    <i class="bi bi-envelope"></i>
                </div>

                <div>
                    <small>Email</small>
                    <strong>{{$user->email}}</strong>
                </div>
            </div>

            <div class="profile-item">
                <div class="profile-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <small>Level</small>
                    <strong>{{$user->level}}</strong>
                </div>
            </div>

            <div class="profile-item">
                <div class="profile-icon">
                    <i class="bi bi-circle-fill"></i>
                </div>

                <div>
                    <small>Status</small>

                    <strong class="status-active">
                        {{$user->status}}
                    </strong>
                </div>
            </div>

        </div>

        <div class="profile-actions">

            <a href="{{route('admin.profile.edit')}}" class="btn btn-success">
                <i class="bi bi-pencil-square me-1"></i>
                Edit Profile
            </a>

            <a href="{{route('admin.password.edit')}}" class="btn btn-outline-secondary">
                <i class="bi bi-key me-1"></i>
                Ubah Password
            </a>

        </div>

    </div>

</div>

@endsection