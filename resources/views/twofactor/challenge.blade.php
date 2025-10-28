{{-- resources/views/twofactor/challenge.blade.php --}}
@extends('layouts.app')

@section('main')
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:480px;">
        <div class="card-body">
            <h5 class="card-title mb-3">Two-Factor Authentication</h5>
            <p>Please enter the 6-digit code from your authenticator app or one of your recovery codes.</p>

            <form method="POST" action="{{ route('twofactor.challenge.verify') }}">
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label">Authentication code</label>
                    <input id="code" name="code" type="text" class="form-control @error('code') is-invalid @enderror" required autofocus>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="trust_device" value="on" id="trust_device">
                    <label class="form-check-label" for="trust_device">
                        Trust this device for 30 days
                    </label>
                </div>

                <button class="btn btn-primary">Verify</button>
                <a href="{{ route('login') }}" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
