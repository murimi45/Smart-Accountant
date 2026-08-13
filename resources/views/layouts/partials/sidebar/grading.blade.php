{{-- Grading module navigation --}}
@module('grading')
    @role('admin', 'teacher')
    <div class="sidebar_blog_2">
        <h4>Grading</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('grading.index') }}" class="{{ Request::is('grading') && !Request::is('grading/*') ? 'active' : '' }}">
                    <i class="fa fa-home purple_color"></i>
                    <span>Grading Home</span>
                </a>
            </li>
            <li>
                <a href="{{ route('grading.assessments.index') }}" class="{{ Request::is('grading/assessments*') ? 'active' : '' }}">
                    <i class="fa fa-list purple_color"></i>
                    <span>Assessments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('grading.mark-entry.index') }}" class="{{ Request::is('grading/mark-entry*') ? 'active' : '' }}">
                    <i class="fa fa-pencil purple_color"></i>
                    <span>Mark Entry</span>
                </a>
            </li>
            <li>
                <a href="{{ route('grading.report-cards.index') }}" class="{{ Request::is('grading/report-cards*') ? 'active' : '' }}">
                    <i class="fa fa-file-pdf-o purple_color"></i>
                    <span>Report Cards</span>
                </a>
            </li>
            @role('admin')
            <li>
                <a href="#gradingSetupMenu" data-toggle="collapse" aria-expanded="{{ Request::is('grading/settings*') || Request::is('grading/subjects*') || Request::is('grading/class-subjects*') || Request::is('grading/assessment-types*') || Request::is('grading/weights*') || Request::is('grading/assignments*') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <i class="fa fa-cog purple_color"></i>
                    <span>Setup</span>
                </a>
                <ul class="collapse list-unstyled {{ Request::is('grading/settings*') || Request::is('grading/subjects*') || Request::is('grading/class-subjects*') || Request::is('grading/assessment-types*') || Request::is('grading/weights*') || Request::is('grading/assignments*') ? 'show' : '' }}" id="gradingSetupMenu">
                    <li><a class="{{ Request::is('grading/settings*') ? 'active' : '' }}" href="{{ route('grading.settings.index') }}">Scheme &amp; Year</a></li>
                    <li><a class="{{ Request::is('grading/subjects*') ? 'active' : '' }}" href="{{ route('grading.subjects.index') }}">Subjects</a></li>
                    <li><a class="{{ Request::is('grading/class-subjects*') ? 'active' : '' }}" href="{{ route('grading.class-subjects.index') }}">Class Subjects</a></li>
                    <li><a class="{{ Request::is('grading/assessment-types*') ? 'active' : '' }}" href="{{ route('grading.assessment-types.index') }}">Assessment Types</a></li>
                    <li><a class="{{ Request::is('grading/weights*') ? 'active' : '' }}" href="{{ route('grading.weights.index') }}">Weights</a></li>
                    <li><a class="{{ Request::is('grading/assignments*') ? 'active' : '' }}" href="{{ route('grading.assignments.index') }}">Teacher Assignments</a></li>
                </ul>
            </li>
            @endrole
        </ul>
    </div>
    @endrole
@endmodule
