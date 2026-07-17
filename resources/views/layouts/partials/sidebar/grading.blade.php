{{-- Populated in Phase 5 (Grading MVP). Visible only when the school is entitled and the user has a grading role. --}}
@module('grading')
    @role('admin', 'teacher')
    <div class="sidebar_blog_2">
        <h4>Grading</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('grading.index') }}" class="{{ Request::is('grading*') ? 'active' : '' }}">
                    <i class="fa fa-pencil-square-o green_color"></i>
                    <span>Grading Home</span>
                </a>
            </li>
        </ul>
    </div>
    @endrole
@endmodule
