@php
    $showStudentLinks = auth()->user()->hasRole('admin');
    $showBulk = auth()->user()->hasRole('admin', 'accountant')
        && auth()->user()->school?->hasModule(\App\Models\Module::SLUG_ACCOUNTANT);
@endphp
@if($showStudentLinks || $showBulk)
<div class="sidebar_blog_2">
    <h4>Students</h4>
    <ul class="list-unstyled components">
        @if($showStudentLinks)
        <li>
            <a href="{{ url('/student') }}" class="{{ Request::is('student*') && !Request::is('bulk*') ? 'active' : '' }}">
                <i class="fa fa-users orange_color"></i>
                <span>Students</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/enrollment') }}" class="{{ Request::is('enrollment*') ? 'active' : '' }}">
                <i class="fa fa-user-plus orange_color"></i>
                <span>Student Enrollment</span>
            </a>
        </li>
        @endif

        @if($showBulk)
        <li>
            <a href="{{ route('bulk.index') }}" class="{{ Request::is('bulk*') ? 'active' : '' }}">
                <i class="fa fa-file-csv green_color"></i>
                <span>Bulk Import/Export</span>
            </a>
        </li>
        @endif
    </ul>
</div>
@endif
