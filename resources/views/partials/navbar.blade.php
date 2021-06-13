<nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0" id="after_alert">
    <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
    </form>
    <ul class="navbar-nav border-left flex-row ">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-nowrap px-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img class="user-avatar rounded-circle mr-2" src="{{asset('assets/images/shards-dashboards-logo.svg')}}" alt="User Avatar">
                <span class="d-none d-md-inline-block">{{Auth::user()->name}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-small">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#"  onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="material-icons text-danger">&#xE879;</i> Logout </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
    <nav class="nav">
        <a href="#" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
            <i class="material-icons">&#xE5D2;</i>
        </a>
    </nav>
</nav>
