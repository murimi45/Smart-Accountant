{{-- Populated in Phase 4 (HR MVP). Visible only when the school is entitled and the user has an HR role. --}}
@module('hr')
    @role('admin', 'hr_manager')
    <div class="sidebar_blog_2">
        <h4>HR</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('hr.index') }}" class="{{ Request::is('hr*') ? 'active' : '' }}">
                    <i class="fa fa-id-badge purple_color"></i>
                    <span>HR Home</span>
                </a>
            </li>
        </ul>
    </div>
    @endrole
@endmodule
