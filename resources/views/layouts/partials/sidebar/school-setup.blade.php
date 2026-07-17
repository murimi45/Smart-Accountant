@role('admin')
<div class="sidebar_blog_2">
    <h4>School Setup</h4>
    <ul class="list-unstyled components">
        <li>
            <a href="#usersMenu" data-toggle="collapse" aria-expanded="{{ Request::is('admins*') || Request::is('accountants*') ? 'true' : 'false' }}" class="dropdown-toggle">
                <i class="fa fa-users purple_color"></i>
                <span>Users</span>
            </a>
            <ul class="collapse list-unstyled {{ Request::is('admins*') || Request::is('accountants*') ? 'show' : '' }}" id="usersMenu">
                <li><a class="{{ Request::is('admins*') ? 'active' : '' }}" href="{{ route('admins.index') }}">Admins</a></li>
                <li><a class="{{ Request::is('accountants*') ? 'active' : '' }}" href="{{ route('accountants.index') }}">Accountants</a></li>
            </ul>
        </li>
        <li>
            <a href="#levelMenu" data-toggle="collapse" aria-expanded="{{ Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'true' : 'false' }}" class="dropdown-toggle">
                <i class="fa fa-graduation-cap purple_color"></i>
                <span>Academic Setup</span>
            </a>
            <ul class="collapse list-unstyled {{ Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'show' : '' }}" id="levelMenu">
                <li><a class="{{ Request::is('class*') ? 'active' : '' }}" href="{{ url('/class') }}">Class Levels</a></li>
                <li><a class="{{ Request::is('term*') ? 'active' : '' }}" href="{{ url('/term') }}">Term Levels</a></li>
                <li><a class="{{ Request::is('streams*') ? 'active' : '' }}" href="{{ route('streams.index') }}">Streams</a></li>
                <li><a class="{{ Request::is('academic-years*') ? 'active' : '' }}" href="{{ url('/academic-years') }}">Academic Years</a></li>
            </ul>
        </li>
        @module('accountant')
        <li>
            <a href="{{ route('payment_channels.index') }}" class="{{ Request::is('payment_channels*') ? 'active' : '' }}">
                <i class="fa fa-cog orange_color"></i>
                <span>Payment Channels</span>
            </a>
        </li>
        @endmodule
    </ul>
</div>
@endrole
