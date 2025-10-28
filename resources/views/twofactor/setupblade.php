{{-- resources/views/twofactor/setup.blade.php --}}
@extends('layouts.app')

@section('main')
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:640px;">
        <div class="card-body">
            <h4 class="card-title mb-3">Two-Factor Authentication Setup</h4>

            <p>Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.).</p>

            <div class="text-center mb-3">
                <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid" />
            </div>

            <p><strong>Secret Key:</strong> <code>{{ $secret }}</code></p>

            <form method="POST" action="{{ route('twofactor.confirm') }}">
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label">Enter the 6-digit code from your app</label>
                    <input id="code" name="code" type="text" class="form-control @error('code') is-invalid @enderror" required autofocus>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">Verify & Enable</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
