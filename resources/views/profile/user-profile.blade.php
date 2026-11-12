@extends('layouts.user_app')

@section('title', 'My Account - Perk Up Coffee')

@section('content')

{{-- Delete this line if the header already appears twice --}}
{{-- @include('dashboard.user-dashboard.partials.header') --}}

@php
    $user       = auth()->user();
    $orderCount = \App\Models\Order::where('user_id', $user->id)->count();
    $favCount   = method_exists($user, 'favorites') ? $user->favorites()->count() : null;
@endphp

<style>
    .acc-page { max-width: 900px; }
    .acc-card { background: #fff; border-radius: 20px; padding: 1.5rem; box-shadow: 0 6px 18px rgba(0,0,0,.08); }
    .acc-avatar {
        width: 72px; height: 72px; border-radius: 50%; background: #843f3f; color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 1.9rem; font-weight: 800; flex-shrink: 0;
    }
    .acc-name { font-weight: 800; font-size: 1.4rem; color: #2b2b2b; margin: 0; }
    .acc-sub { color: #8a8a8a; font-size: .92rem; }
    .acc-stat { background: #faf6f6; border-radius: 14px; padding: .9rem 1rem; text-align: center; height: 100%; text-decoration: none; display: block; color: inherit; }
    .acc-stat:hover { background: #f3e8e8; color: inherit; }
    .acc-stat b { display: block; font-size: 1.5rem; color: #843f3f; }
    .acc-stat span { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #8a8a8a; }
    .acc-title { font-weight: 800; color: #2b2b2b; margin-bottom: .2rem; }
    .acc-hint { color: #8a8a8a; font-size: .9rem; margin-bottom: 1rem; }
    .btn-brand { background: #843f3f; color: #fff; border: none; border-radius: 999px; padding: .55rem 1.6rem; font-weight: 600; }
    .btn-brand:hover { background: #6a3232; color: #fff; }
</style>

<div class="container acc-page py-4">

    {{-- Saved messages --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show">Your information was saved.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show">Your password was changed.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <h1 class="fw-bold mb-4" style="color:#2b2b2b;">My Account</h1>

    {{-- 1. Summary --}}
    <div class="acc-card mb-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="acc-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            <div class="flex-grow-1">
                <p class="acc-name">{{ $user->name }}</p>
                <div class="acc-sub">{{ $user->email }}</div>
                <div class="acc-sub">Customer since {{ $user->created_at->format('M Y') }}</div>
            </div>

            @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary rounded-pill px-4">Log out</button>
                </form>
            @endif
        </div>

        <div class="row g-3 mt-2">
            <div class="col-6 col-md-4">
                <a class="acc-stat" href="{{ route('order.history') }}">
                    <b>{{ $orderCount }}</b><span>My orders</span>
                </a>
            </div>
            @if (!is_null($favCount))
                <div class="col-6 col-md-4">
                    <a class="acc-stat" href="{{ route('favorites.index') }}">
                        <b>{{ $favCount }}</b><span>Favorites</span>
                    </a>
                </div>
            @endif
            <div class="col-12 col-md-4">
                <a class="acc-stat" href="{{ route('cart.show') }}">
                    <b>{{ collect(session('cart', []))->sum() }}</b><span>In my cart</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Edit name and email --}}
    <div class="acc-card mb-4">
        <h5 class="acc-title">Profile information</h5>
        <p class="acc-hint">Update your name and email address.</p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-brand">Save</button>
        </form>
    </div>

    {{-- 3. Change password --}}
    <div class="acc-card mb-4">
        <h5 class="acc-title">Change password</h5>
        <p class="acc-hint">Use a long password you don't use anywhere else.</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label" for="current_password">Current password</label>
                <input id="current_password" name="current_password" type="password"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                       autocomplete="current-password">
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">New password</label>
                <input id="password" name="password" type="password"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                       autocomplete="new-password">
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="form-control" autocomplete="new-password">
            </div>

            <button type="submit" class="btn-brand">Change password</button>
        </form>
    </div>

    {{-- 4. Delete account --}}
    <div class="acc-card mb-4 border border-danger-subtle">
        <h5 class="acc-title text-danger">Delete account</h5>
        <p class="acc-hint">This permanently removes your account. Enter your password to confirm.</p>

        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Delete your account for good?')">
            @csrf
            @method('DELETE')

            <div class="mb-3">
                <input name="password" type="password" placeholder="Your password"
                       class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif">
                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
            </div>

            <button type="submit" class="btn btn-danger rounded-pill px-4">Delete my account</button>
        </form>
    </div>
</div>

@endsection