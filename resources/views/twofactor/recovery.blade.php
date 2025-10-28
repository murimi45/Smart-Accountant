{{-- resources/views/twofactor/recovery.blade.php --}}
@extends('layouts.app')

@section('main')
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:640px;">
        <div class="card-body">
            <h4 class="card-title mb-3">Recovery Codes</h4>

            <p>Please store these recovery codes in a safe place. Each code can be used once to log in if you lose access to your authenticator app.</p>

            <ul class="list-group mb-3">
                @foreach ($codes as $code)
                    <li class="list-group-item"><code>{{ $code }}</code></li>
                @endforeach
            </ul>

            <a href="{{ route('dashboard') }}" class="btn btn-primary">Continue</a>
        </div>
    </div>
</div>
@endsection
