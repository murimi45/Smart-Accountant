@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Extra Fee Assignments</h4>
                <p class="text-muted mb-0 mt-1">Manage student extra fee assignments</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('assignextrafee') }}" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
                    <i class="fa fa-plus me-2"></i>Assign Extra Fee
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #28a745; border-radius: 8px;">
            <i class="fa fa-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545; border-radius: 8px;">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search Filters Card --}}
    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: #fafbfc; padding: 20px 25px; border-bottom: 1px solid #e8eaed; border-radius: 12px 12px 0 0;">
                    <div class="heading1 margin_0">
                        <h2 style="font-size: 18px; color: #2c3e50; font-weight: 600; margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-filter me-2" style="color: #8e68ef;"></i>Search Filters
                        </h2>
                    </div>
                </div>

                <div class="full inner_elements" style="padding: 25px;">
                    <form method="GET" action="{{ route('listextrafeestudents') }}" class="row g-3">
                        {{-- Extra Fee Filter --}}
                        <div class="col-md-4">
                            <label for="extra_fee_id" class="form-label fw-semibold" style="font-size: 13px; color: #6c757d; margin-bottom: 8px;">
                                <i class="fa fa-receipt me-1"></i>Extra Fee
                            </label>
                            <select name="extra_fee_id" id="extra_fee_id" class="form-select" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 14px;">
                                <option value="">All Extra Fees</option>
                                @foreach($extraFees as $extraFee)
                                    <option value="{{ $extraFee->id }}" {{ request('extra_fee_id') == $extraFee->id ? 'selected' : '' }}>
                                        {{ $extraFee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Student Name Filter --}}
                        <div class="col-md-4">
                            <label for="student_name" class="form-label fw-semibold" style="font-size: 13px; color: #6c757d; margin-bottom: 8px;">
                                <i class="fa fa-user me-1"></i>Search Student
                            </label>
                            <input type="text" 
                                   name="student_name" 
                                   id="student_name" 
                                   value="{{ request('student_name') }}" 
                                   class="form-control" 
                                   placeholder="Name or Admission No."
                                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 14px;">
                        </div>

                        {{-- Buttons --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: #6c757d; margin-bottom: 8px;">
                                <i class="fa fa-cog me-1"></i>Actions
                            </label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill" style="border-radius: 8px;">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('listextrafeestudents') }}" class="btn btn-secondary flex-fill" style="border-radius: 8px;">
                                    <i class="fa fa-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Assignment List Table --}}
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header">
                    <h5>
                        <i class="fa fa-users me-2" style="color: #8e68ef;"></i>Students Assigned
                        <span class="badge bg-primary ms-2" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                            {{ count($extraFeeStudents) }} Total
                        </span>
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-receipt me-2"></i>Extra Fee</th>
                                    <th><i class="fa fa-user me-2"></i>Student Name</th>
                                    <th><i class="fa fa-calendar me-2"></i>Term</th>
                                    <th><i class="fa fa-calendar-alt me-2"></i>Year</th>
                                    <th><i class="fa fa-dollar-sign me-2"></i>Amount</th>
                                    <th><i class="fa fa-sort-numeric-up me-2"></i>Quantity</th>
                                    <th><i class="fa fa-user-shield me-2"></i>Created By</th>
                                    <th style="min-width: 180px;"><i class="fa fa-cog me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extraFeeStudents as $extraFeeStudent)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="fee-icon me-3" style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #8e68ef 0%, #7344e8 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                                                {{ strtoupper(substr($extraFeeStudent->extraFee->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $extraFeeStudent->extraFee->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                                {{ strtoupper(substr($extraFeeStudent->student->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $extraFeeStudent->student->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ $extraFeeStudent->extraFee->term->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark" style="padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ $extraFeeStudent->extraFee->year }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: linear-gradient(135deg, #79c347 0%, #5fa732 100%); color: white; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                            <i class="fa fa-coins me-1"></i>KSh {{ number_format($extraFeeStudent->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ $extraFeeStudent->quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="creator-avatar me-2" style="width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #fabb3d 0%, #f9a825 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 11px;">
                                                {{ strtoupper(substr($extraFeeStudent->creator->admin_name, 0, 1)) }}
                                            </div>
                                            <span style="font-size: 13px; color: #495057;">{{ $extraFeeStudent->creator->admin_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/assign-extra-fee/edit/' . $extraFeeStudent->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               style="border-radius: 6px; padding: 6px 14px;" 
                                               title="Edit Assignment">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ url('/assign-extra-fee/delete/' . $extraFeeStudent->id) }}" 
                                               class="btn btn-sm btn-danger" 
                                               style="border-radius: 6px; padding: 6px 14px;"
                                               onclick="return confirm('Are you sure you want to delete this assignment?')" 
                                               title="Delete Assignment">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div style="color: #9ca3af;">
                                            <i class="fa fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                            <p class="mb-0">No assignments found</p>
                                            <small>Click "Assign Extra Fee" to create one</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Form Control Focus */
.form-select:focus,
.form-control:focus {
    border-color: #8e68ef;
    box-shadow: 0 0 0 0.2rem rgba(142, 104, 239, 0.15);
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(54, 169, 226, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(121, 195, 71, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #ff4748 0%, #e63946 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 71, 72, 0.3);
}

.btn-secondary {
    background: #6c757d;
    border: none;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Responsive Design */
@media (max-width: 768px) {
    .fee-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 12px !important;
    }

    .user-avatar,
    .creator-avatar {
        width: 28px !important;
        height: 28px !important;
        font-size: 11px !important;
    }
    
    .btn-sm {
        padding: 4px 10px !important;
        font-size: 12px;
    }
    
    .table {
        font-size: 13px;
    }

    .badge {
        font-size: 11px !important;
        padding: 4px 8px !important;
    }
}
</style>

@endsection