<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">منو</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            @guest
            <li class="dropdown">
                <a href="{{ route('login') }}">
                    <span>{{ __('Login') }}</span>
                </a>
            </li>
            @if (Route::has('register'))
            <li class="dropdown">
                <a href="{{ route('register') }}">
                    <span>{{ __('Register') }}</span>
                </a>
            </li>
            @endif
            @else
            <li class="dropdown">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
            @endguest
        </ul>
    </div>
</nav>