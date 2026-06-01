@extends('layouts.app')
@section('main')

<div class="main-wrapper">

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <h4 class="mb-1">Student Enrollment</h4>
        <p class="text-muted mb-0">Manage student enrollment status and run school-wide promotions</p>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="card actions-card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                {{-- Left: Term indicator + school-wide counts --}}
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div class="text-muted" style="font-size:12px;">Current term</div>
                        <div style="font-size:15px;font-weight:600;color:var(--gray-900);">
                            {{ $activeTerm ? $activeTerm->name : 'No active term' }}
                            @if($activeTerm) &mdash; {{ $activeTerm->year }} @endif
                        </div>
                    </div>

                   @if($nextTerm)
                        <div style="font-size:18px;color:var(--gray-300);">→</div>
                        <div>
                            <div class="text-muted" style="font-size:12px;">Promoting to</div>
                            <div style="font-size:15px;font-weight:600;color:var(--success-dark);">
                                {{ $nextTerm->name }}
                            </div>
                        </div>
                    @endif

                    <div class="vr" style="height:36px;opacity:.15;"></div>
                    <div class="d-flex gap-3" style="font-size:13px;">
                        <span>
                            <span class="stat-dot dot-promote"></span>
                            <span style="font-weight:600;">{{ $counts->promoting ?? 0 }}</span> promoting
                        </span>
                        <span>
                            <span class="stat-dot dot-repeat"></span>
                            <span style="font-weight:600;">{{ $counts->repeating ?? 0 }}</span> repeating
                        </span>
                        <span>
                            <span class="stat-dot dot-inactive"></span>
                            <span style="font-weight:600;">{{ $counts->inactive ?? 0 }}</span> inactive
                        </span>
                        @if(($counts->needs_correction ?? 0) > 0)
                            <span>
                                <span class="stat-dot" style="background:#ef4444;"></span>
                                <span style="font-weight:600;color:#991b1b;">{{ $counts->needs_correction }}</span>
                                <span style="color:#991b1b;">need correction</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right: Promote buttons --}}
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-success btn-action"
                            data-bs-toggle="modal" data-bs-target="#promoteTermModal"
                            @if(!$nextTerm) disabled title="Create the next term before promoting" @endif>
                        <i class="fa fa-arrow-right me-1"></i>Promote to next term
                    </button>
                    <button type="button" class="btn btn-primary btn-action"
                            data-bs-toggle="modal" data-bs-target="#promoteClassModal">
                        <i class="fa fa-graduation-cap me-1"></i>Promote to next year
                    </button>
                </div>
            </div>

            {{-- Scope note --}}
            <div class="scope-note mt-3">
                <i class="fa fa-info-circle me-1"></i>
                Promote buttons act on the <strong>entire school</strong> — not just the class you are viewing. Filters are for navigation only.
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('enrollment.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-school me-1"></i>Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-select">
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}"
                                    {{ $termId == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} — {{ $term->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><i class="fa fa-filter me-1"></i>Status</label>
                        <select name="status" class="form-select">
                            <option value="">All statuses</option>
                            <option value="active"           {{ request('status') === 'active'           ? 'selected' : '' }}>Promoting</option>
                            <option value="repeating"        {{ request('status') === 'repeating'        ? 'selected' : '' }}>Repeating</option>
                            <option value="inactive"         {{ request('status') === 'inactive'         ? 'selected' : '' }}>Inactive</option>
                            <option value="wrongly_promoted" {{ request('status') === 'wrongly_promoted' ? 'selected' : '' }}>Needs correction</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-search me-1"></i>Search student</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Name or admission no."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('enrollment.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Enrollment Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-users me-2"></i>Student enrollment list</h5>
                <span class="badge bg-light text-dark">{{ $enrollments->total() }} Students</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table enrollment-table mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Current class</th>
                            <th>Stream</th>
                            <th>Next class</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            @php
                                $student    = $enrollment->student;
                                $class      = $enrollment->schoolClass;
                                $stream     = $enrollment->stream;
                                $isWrong    = $enrollment->needsCorrection();
                                $isRepeat   = $enrollment->status === 'repeating';
                                $isInactive = $enrollment->status === 'inactive';
                                $rowClass   = $isWrong    ? 'row-wrong'
                                            : ($isRepeat  ? 'row-repeat'
                                            : ($isInactive? 'row-inactive' : ''));
                            @endphp

                            <tr class="{{ $rowClass }}"
                                data-status="{{ $enrollment->status }}"
                                data-class="{{ $class ? $class->id : '' }}">

                                {{-- Student --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar {{ $isWrong ? 'avatar-danger' : ($isInactive ? 'avatar-inactive' : '') }}">
                                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="student-name {{ $isInactive ? 'text-muted' : '' }}">
                                                {{ $student->full_name }}
                                            </div>
                                            <div class="student-id">{{ $student->admission }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Current class --}}
                                <td><span class="badge-class">{{ $class?->name ?? '—' }}</span></td>

                                {{-- Stream --}}
                                <td>{{ $stream?->name ? 'Stream '.$stream->name : '—' }}</td>

                                {{-- Next class --}}
                                <td>
                                    @if($isWrong)
                                        <span class="text-danger" style="font-size:12px;">
                                            <i class="fa fa-exclamation-circle me-1"></i>
                                            {{ $class?->name }}{{ $stream?->name }} (wrong)
                                        </span>
                                    @elseif($isInactive)
                                        <span class="text-muted">—</span>
                                    @else
                                        @if($isRepeat)
                                            <em class="text-warning-muted">
                                                {{ $enrollment->nextClassLabel() }}
                                            </em>
                                        @else
                                            {{ $enrollment->nextClassLabel() }}
                                        @endif
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($isWrong)
                                        <span class="badge-status status-wrong">
                                            <i class="fa fa-exclamation-triangle me-1"></i>Needs correction
                                        </span>
                                    @else
                                        <form method="POST"
                                              action="{{ route('enrollment.update-status', $enrollment->id) }}"
                                              id="status-form-{{ $enrollment->id }}">
                                            @csrf
                                            <select name="status"
                                                    class="form-select status-select"
                                                    onchange="document.getElementById('status-form-{{ $enrollment->id }}').submit()">
                                                <option value="active"    {{ $enrollment->status === 'active'    ? 'selected' : '' }}>Promote</option>
                                                <option value="repeating" {{ $enrollment->status === 'repeating' ? 'selected' : '' }}>Repeating</option>
                                                <option value="inactive"  {{ $enrollment->status === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td>
                                    @if($isWrong)
                                        <button type="button"
                                                class="btn btn-sm btn-danger-outline"
                                                data-bs-toggle="modal"
                                                data-bs-target="#correctionModal"
                                                data-enrollment-id="{{ $enrollment->id }}"
                                                data-student="{{ $student->full_name }}"
                                                data-admission="{{ $student->admission }}"
                                                data-wrong-class="{{ $class?->name }}{{ $stream?->name }}"
                                                data-from-class="{{ $enrollment->promotedFrom?->schoolClass?->name ?? '—' }}"
                                                onclick="openCorrection(this)">
                                            <i class="fa fa-wrench me-1"></i>Correct
                                        </button>
                                    @else
                                        <span class="text-muted" style="font-size:12px;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-users fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="mb-0 text-muted">No enrollments found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($enrollments->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>


{{-- ===================== MODALS ===================== --}}

{{-- Promote to next TERM modal --}}
<div class="modal fade" id="promoteTermModal" tabindex="-1" aria-labelledby="promoteTermLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('promotion.term') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="promoteTermLabel">
                        <i class="fa fa-arrow-right me-2"></i>Promote whole school — next term
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Live counts from controller --}}
                    <div class="confirm-summary mb-4">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val text-success">{{ $counts->promoting ?? 0 }}</div>
                                    <div class="confirm-lbl">Promoting</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val text-warning">{{ $counts->repeating ?? 0 }}</div>
                                    <div class="confirm-lbl">Repeating</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val text-muted">{{ $counts->inactive ?? 0 }}</div>
                                    <div class="confirm-lbl">Skipped (inactive)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>From term</label>
                        <select name="from_term_id" id="promote-from-term" class="form-select" required>
                            @foreach($termsOrdered as $term)
                                <option value="{{ $term->id }}"
                                    {{ ($promotionFromTermId ?? null) == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} — {{ $term->year }}
                                    @if($term->active) (current) @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Defaults to the school's current term</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-calendar-check me-1"></i>To term</label>
                        <select name="to_term_id" id="promote-to-term" class="form-select" required
                            @if(!$promotionNextTerm) disabled @endif>
                            @if($promotionNextTerm)
                                <option value="{{ $promotionNextTerm->id }}" selected>
                                    {{ $promotionNextTerm->name }} — {{ $promotionNextTerm->year }}
                                </option>
                            @else
                                <option value="">No next term — create the next term first</option>
                            @endif
                        </select>
                        <small class="text-muted">Only the term immediately after the selected from term</small>
                    </div>
                    <div class="alert-info-box">
                        <i class="fa fa-info-circle me-2"></i>
                        Inactive students are skipped entirely. Repeating students receive a new enrollment and invoice in the same class.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" id="promote-term-submit" class="btn btn-outline-success"
                        @if(!$promotionNextTerm) disabled @endif>
                        <i class="fa fa-check-circle me-1"></i>Confirm promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Promote to next YEAR modal --}}
<div class="modal fade" id="promoteClassModal" tabindex="-1" aria-labelledby="promoteClassLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('promotion.class') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="promoteClassLabel">
                        <i class="fa fa-graduation-cap me-2"></i>Promote whole school — next year
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="confirm-summary mb-4">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val text-success">{{ $counts->promoting ?? 0 }}</div>
                                    <div class="confirm-lbl">Moving up</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val text-warning">{{ $counts->repeating ?? 0 }}</div>
                                    <div class="confirm-lbl">Repeating</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="confirm-stat">
                                    <div class="confirm-val" style="color:var(--primary-color);">
                                        {{ $counts->graduating ?? 0 }}
                                    </div>
                                    <div class="confirm-lbl">Graduating</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Target academic year</label>
                        @if($academicYears->isEmpty())
                            <p class="text-danger mb-2" style="font-size:13px;">
                                No academic year exists after
                                <strong>{{ $currentAcademicYear?->name ?? 'the current year' }}</strong>.
                                Create the next year and its first term before promoting.
                            </p>
                        @else
                            <select name="academic_year" class="form-select" required>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->name }}"
                                        {{ ($nextAcademicYear && $nextAcademicYear->id === $ay->id) ? 'selected' : '' }}>
                                        {{ $ay->name }}
                                        @if($nextAcademicYear && $nextAcademicYear->id === $ay->id) (next) @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($currentAcademicYear)
                                <small class="text-muted d-block mt-1">
                                    Promoting from <strong>{{ $currentAcademicYear->name }}</strong> — only later years are listed.
                                </small>
                            @endif
                        @endif
                    </div>
                    <div class="alert-info-box">
                        <i class="fa fa-info-circle me-2"></i>
                        Each student moves to the next class level. Stream is carried forward automatically.
                        Students in the final class will be <strong>graduated</strong>.
                    </div>
                    <div class="alert-warning-box mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Ensure the first term for the target year has been created before proceeding.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" @if($academicYears->isEmpty()) disabled @endif>
                        <i class="fa fa-graduation-cap me-1"></i>Confirm year promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Correction modal --}}
<div class="modal fade" id="correctionModal" tabindex="-1" aria-labelledby="correctionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="correction-form" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="correctionLabel">
                        <i class="fa fa-wrench me-2"></i>Correct wrong enrollment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Student info --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar avatar-danger" id="modal-avatar">?</div>
                        <div>
                            <div style="font-weight:600;font-size:15px;" id="modal-student-name"></div>
                            <div class="student-id" id="modal-admission"></div>
                        </div>
                    </div>

                    {{-- Info grid --}}
                    <div class="info-grid mb-3">
                        <div class="info-row">
                            <span class="info-label">Wrongly placed in</span>
                            <span class="info-val text-danger" id="modal-wrong-class"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Was promoted from</span>
                            <span class="info-val" id="modal-from-class"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Invoice impact</span>
                            <span class="info-val text-danger">Current invoice will be <strong>voided</strong></span>
                        </div>
                    </div>

                    <div class="alert-danger-box mb-3">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        The existing enrollment will be <strong>cancelled</strong> and its invoice <strong>voided</strong>.
                        A new enrollment and invoice will be created. Old records are kept for audit.
                    </div>

                    {{-- Correction fields --}}
                    <div class="mb-3">
                        <label class="form-label">Correct class</label>
                        <select name="correct_class_id" class="form-select" required>
                            <option value="">Select correct class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Correct stream
                            <span class="text-muted">(optional — leave blank to keep current)</span>
                        </label>
                        <select name="correct_stream_id" class="form-select">
                            <option value="">Keep same stream</option>
                            {{-- Streams loaded dynamically if you have a Stream model --}}
                            {{-- @foreach($streams as $stream)
                                <option value="{{ $stream->id }}">Stream {{ $stream->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Reason for correction <span class="text-danger">*</span>
                        </label>
                        <textarea name="correction_reason"
                                  class="form-control"
                                  rows="2"
                                  placeholder="e.g. Student should be repeating Class 3 per head teacher decision"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-check-circle me-1"></i>Confirm correction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

.page-header h4 { font-size: 24px; font-weight: 600; color: var(--gray-900); margin: 0; }
.page-header p { font-size: 14px; color: var(--gray-500); }

.filter-card, .actions-card, .table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}

.filter-card .card-body, .actions-card .card-body { padding: 20px; }
.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.scope-note {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: var(--border-radius);
    padding: 10px 14px;
    font-size: 13px;
    color: #1e40af;
}

.stat-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 5px; }
.dot-promote { background: var(--success-color); }
.dot-repeat  { background: var(--warning-color); }
.dot-inactive{ background: var(--gray-300); }

.form-label { font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 6px; }
.form-control, .form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color .2s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54,169,226,.1);
}

.btn { border-radius: var(--border-radius); padding: 8px 16px; font-size: 14px; font-weight: 500; transition: all .2s; }
.btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
.btn-primary:hover { background-color: #2a8cbd; border-color: #2a8cbd; }
.btn-outline-success { color: var(--success-dark); border-color: var(--success-color); background: white; }
.btn-outline-success:hover { background: #e8f5e0; color: var(--success-dark); }
.btn-action { font-size: 13px; }
.btn-danger { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.btn-danger:hover { background: #fecaca; }
.btn-danger-outline {
    font-size: 12px; padding: 5px 12px;
    background: #fee2e2; color: #991b1b;
    border: 1px solid #fca5a5;
    border-radius: var(--border-radius);
}
.btn-danger-outline:hover { background: #fecaca; }

.enrollment-table { font-size: 14px; }
.enrollment-table thead { background-color: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
.enrollment-table thead th {
    font-weight: 600; color: var(--gray-700);
    padding: 12px 16px; font-size: 13px;
    text-transform: uppercase; letter-spacing: .3px;
}
.enrollment-table tbody td {
    padding: 14px 16px; vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}
.enrollment-table tbody tr:hover { background-color: var(--gray-50); }

.row-repeat   { background: #fffbeb; }
.row-inactive { opacity: .65; }
.row-wrong    { background: #fff1f2; }
.text-warning-muted { color: #b45309; font-size: 13px; }

.avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background-color: var(--primary-color); color: white;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 16px; flex-shrink: 0;
}
.avatar-danger   { background: #fee2e2; color: #991b1b; }
.avatar-inactive { background: var(--gray-200); color: var(--gray-500); }

.student-name { font-weight: 500; color: var(--gray-900); }
.student-id   { font-size: 12px; color: var(--gray-500); margin-top: 2px; }

.badge-class {
    background: var(--gray-100); color: var(--gray-700);
    padding: 4px 10px; border-radius: 6px;
    font-weight: 500; font-size: 12px; display: inline-block;
}

.status-select { font-size: 12px !important; padding: 5px 8px !important; width: auto; min-width: 120px; }

.badge-status { padding: 4px 10px; border-radius: 6px; font-weight: 500; font-size: 12px; display: inline-block; }
.status-wrong { background: #fee2e2; color: #991b1b; }

.confirm-summary {
    background: var(--gray-50); border: 1px solid var(--gray-200);
    border-radius: var(--border-radius); padding: 16px;
}
.confirm-stat .confirm-val { font-size: 22px; font-weight: 700; }
.confirm-stat .confirm-lbl { font-size: 12px; color: var(--gray-500); margin-top: 2px; }

.alert-info-box {
    background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: var(--border-radius); padding: 10px 14px; font-size: 13px; color: #1e40af;
}
.alert-warning-box {
    background: #fffbeb; border: 1px solid #fde68a;
    border-radius: var(--border-radius); padding: 10px 14px; font-size: 13px; color: #92400e;
}
.alert-danger-box {
    background: #fee2e2; border: 1px solid #fca5a5;
    border-radius: var(--border-radius); padding: 10px 14px; font-size: 13px; color: #991b1b;
}

.info-grid { border: 1px solid var(--gray-200); border-radius: var(--border-radius); overflow: hidden; }
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 14px; border-bottom: 1px solid var(--gray-100); font-size: 13px;
}
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--gray-500); }
.info-val   { font-weight: 500; color: var(--gray-900); }

.modal-content { border: none; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.15); }
.modal-header  { border-bottom: 1px solid var(--gray-200); padding: 20px 24px; }
.modal-body    { padding: 24px; }
.modal-footer  { border-top: 1px solid var(--gray-200); padding: 16px 24px; }

.alert { border-radius: var(--border-radius); border: none; padding: 12px 16px; }
.alert-success { background: #e8f5e0; color: #3d7a1f; }
.alert-danger  { background: #fee2e2; color: #991b1b; }
.alert-warning { background: #fffbeb; color: #92400e; }

@media (max-width: 768px) {
    .avatar { width: 32px; height: 32px; font-size: 14px; }
    .enrollment-table thead th,
    .enrollment-table tbody td { padding: 10px; }
}
</style>

<script>
const termNextMap = @json($termNextMap);
const termLabels  = @json($termLabels);

function syncPromoteToTerm() {
    const fromSelect = document.getElementById('promote-from-term');
    const toSelect   = document.getElementById('promote-to-term');
    const submitBtn  = document.getElementById('promote-term-submit');
    if (!fromSelect || !toSelect) return;

    const nextId = termNextMap[fromSelect.value];
    toSelect.innerHTML = '';

    if (nextId) {
        const opt = document.createElement('option');
        opt.value = nextId;
        opt.textContent = termLabels[nextId] || 'Next term';
        opt.selected = true;
        toSelect.appendChild(opt);
        toSelect.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
    } else {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'No next term — create the next term first';
        toSelect.appendChild(opt);
        toSelect.disabled = true;
        if (submitBtn) submitBtn.disabled = true;
    }
}

document.getElementById('promote-from-term')?.addEventListener('change', syncPromoteToTerm);

// Correction modal — populate from data attributes on the Correct button
function openCorrection(btn) {
    const enrollmentId = btn.dataset.enrollmentId;

    // Set the form action dynamically to the correct enrollment
    document.getElementById('correction-form').action =
        '/enrollment/' + enrollmentId + '/correction';

    document.getElementById('modal-student-name').textContent = btn.dataset.student;
    document.getElementById('modal-admission').textContent    = btn.dataset.admission;
    document.getElementById('modal-wrong-class').textContent  = btn.dataset.wrongClass;
    document.getElementById('modal-from-class').textContent   = btn.dataset.fromClass;
    document.getElementById('modal-avatar').textContent       = btn.dataset.student.charAt(0).toUpperCase();
}
</script>

@endsection