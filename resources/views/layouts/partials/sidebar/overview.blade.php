<div class="sidebar_blog_2">
    <h4>Overview</h4>
    <ul class="list-unstyled components">
        <li>
            <a href="{{ url('/dashboard') }}"
               class="{{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                <i class="fa fa-tachometer blue_color"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>
</div>
