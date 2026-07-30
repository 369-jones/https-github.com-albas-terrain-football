<div id="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-icon.svg') }}" alt="{{ config('app.name') }}" class="sidebar-logo-img">
        <div>
            <div class="sidebar-title">{{ config('app.name') }}</div>
            <div class="sidebar-sub">Admin</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        {{-- PRINCIPAL --}}
        <div class="nav-section">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gauge"></i></span>
            <span>Tableau de bord</span>
        </a>

        {{-- RESERVATIONS --}}
        <div class="nav-section">Réservations</div>

        <a href="{{ route('calendrier') }}"
           class="nav-item {{ request()->routeIs('calendrier') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span>
            <span>Calendrier</span>
        </a>

        <a href="{{ route('reservations.index') }}"
           class="nav-item {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span>
            <span>Réservations</span>
        </a>

        <a href="{{ route('reservations.create') }}"
           class="nav-item {{ request()->routeIs('reservations.create') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-plus"></i></span>
            <span>Nouvelle réservation</span>
        </a>

        <a href="{{ route('admin.bookings') }}"
           class="nav-item {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-futbol"></i></span>
            <span>Réservations Stades</span>
        </a>

        {{-- FINANCES --}}
        <div class="nav-section">Finances</div>

        <a href="{{ route('paiements.index') }}"
           class="nav-item {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-sack-dollar"></i></span>
            <span>Paiements</span>
        </a>

        <a href="{{ route('factures.index') }}"
           class="nav-item {{ request()->routeIs('factures.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span>
            <span>Factures & Reçus</span>
        </a>

        {{-- GESTION --}}
        <div class="nav-section">Gestion</div>

        <a href="{{ route('equipes.index') }}"
           class="nav-item {{ request()->routeIs('equipes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-people-group"></i></span>
            <span>Équipes</span>
        </a>

        <a href="{{ route('rapports') }}"
           class="nav-item {{ request()->routeIs('rapports') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>Rapports</span>
        </a>

        <a href="{{ route('parametres') }}"
           class="nav-item {{ request()->routeIs('parametres') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
            <span>Paramètres</span>
        </a>

        <a href="{{ route('aide') }}"
           class="nav-item {{ request()->routeIs('aide') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-question"></i></span>
            <span>Aide</span>
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
                <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
            </button>
        </form>
    </div>

</div>
