<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Platform — School modules</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">School modules</h1>
            <p class="text-muted mb-0">Enable or disable product modules per school.</p>
        </div>
        <a href="/login" class="btn btn-outline-secondary btn-sm">Back to login</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>School</th>
                        <th>Email</th>
                        @foreach($modules as $module)
                            <th>{{ $module->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td>{{ $school->school_name }}</td>
                            <td>{{ $school->email }}</td>
                            @foreach($modules as $module)
                                @php
                                    $pivot = $school->schoolModules->firstWhere('module_id', $module->id);
                                    $entitled = $pivot && $pivot->isEntitled();
                                @endphp
                                <td>
                                    @if($entitled)
                                        <span class="badge badge-success">{{ $pivot->status }}</span>
                                        <form method="POST" action="{{ route('platform.school-modules.disable', [$school, $module->slug]) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Disable</button>
                                        </form>
                                    @else
                                        <span class="badge badge-secondary">off</span>
                                        <form method="POST" action="{{ route('platform.school-modules.enable', [$school, $module->slug]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Enable</button>
                                        </form>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $modules->count() }}">No schools found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $schools->links() }}
    </div>
</div>
</body>
</html>
