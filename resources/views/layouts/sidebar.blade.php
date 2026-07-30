<div id="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-icon.svg') }}" alt="{{ config('app.name') }}" class="sidebar-logo-img">
        <div>
            <div class="sidebar-title">{{ config('app.name') }}</div>
            <div class="sidebar-sub">{{ __('Admin') }}</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        {{-- PRINCIPAL --}}
        <div class="nav-section">{{ __('Main') }}</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gauge"></i></span>
            <span>{{ __('Dashboard') }}</span>
        </a>

        {{-- RESERVATIONS --}}
        <div class="nav-section">{{ __('Bookings') }}</div>

        <a href="{{ route('calendrier') }}"
           class="nav-item {{ request()->routeIs('calendrier') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span>
            <span>{{ __('Calendar') }}</span>
        </a>

        <a href="{{ route('reservations.index') }}"
           class="nav-item {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span>
            <span>{{ __('Bookings') }}</span>
        </a>

        <a href="{{ route('reservations.create') }}"
           class="nav-item {{ request()->routeIs('reservations.create') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-plus"></i></span>
            <span>{{ __('New booking') }}</span>
        </a>

        <a href="{{ route('admin.bookings') }}"
           class="nav-item {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-futbol"></i></span>
            <span>{{ __('Stadium bookings') }}</span>
        </a>

        {{-- FINANCES --}}
        <div class="nav-section">{{ __('Finances') }}</div>

        <a href="{{ route('paiements.index') }}"
           class="nav-item {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-sack-dollar"></i></span>
            <span>{{ __('Payments') }}</span>
        </a>

        <a href="{{ route('factures.index') }}"
           class="nav-item {{ request()->routeIs('factures.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span>
            <span>{{ __('Invoices & receipts') }}</span>
        </a>

        {{-- GESTION --}}
        <div class="nav-section">{{ __('Management') }}</div>

        <a href="{{ route('equipes.index') }}"
           class="nav-item {{ request()->routeIs('equipes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-people-group"></i></span>
            <span>{{ __('Teams') }}</span>
        </a>

        <a href="{{ route('rapports') }}"
           class="nav-item {{ request()->routeIs('rapports') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>{{ __('Reports') }}</span>
        </a>

        <a href="{{ route('parametres') }}"
           class="nav-item {{ request()->routeIs('parametres') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
            <span>{{ __('Settings') }}</span>
        </a>

        <a href="{{ route('aide') }}"
           class="nav-item {{ request()->routeIs('aide') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-question"></i></span>
            <span>{{ __('Help') }}</span>
        </a>

        {{-- ESPACE PROPRIETAIRE --}}
        <div class="nav-section">{{ __('Owner area') }}</div>

        <a href="{{ route('bookings.mine') }}"
           class="nav-item {{ request()->routeIs('bookings.mine') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span>{{ __('My bookings') }}</span>
        </a>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-store"></i></span>
            <span>{{ __('Owner dashboard') }}</span>
        </a>

        <a href="{{ route('admin.staff.index') }}"
           class="nav-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-user-tie"></i></span>
            <span>{{ __('Stadium managers') }}</span>
        </a>

    </nav>

    {{-- FOOTER SIDEBAR --}}
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-email">{{ Auth::user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> {{ __('Log out') }}
            </button>
        </form>
    </div>

</div>
