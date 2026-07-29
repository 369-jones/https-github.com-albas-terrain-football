<div id="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-header">
        <div class="sidebar-logo">⚽</div>
        <div>
            <div class="sidebar-title">Terrain Football</div>
            <div class="sidebar-sub">Universitaire</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        @php
            $notifCount = \App\Models\Notification::nonLues()->count();
        @endphp

        <a href="{{ route('notifications.index') }}"
            class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <span class="nav-icon">🔔</span>
            <span>Notifications</span>
            @if($notifCount > 0)
            <span id="notif-badge" style="
                margin-left:auto;background:#dc2626;color:white;
                border-radius:20px;font-size:10px;font-weight:800;
                padding:2px 7px;
            ">{{ $notifCount }}</span>
            @endif
        </a>

        {{-- PRINCIPAL --}}
        <div class="nav-section">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span>
            <span>Tableau de bord</span>
        </a>

        {{-- RESERVATIONS --}}
        <div class="nav-section">Réservations</div>

        <a href="{{ route('calendrier') }}"
           class="nav-item {{ request()->routeIs('calendrier') ? 'active' : '' }}">
            <span class="nav-icon">📅</span>
            <span>Calendrier</span>
        </a>

        <a href="{{ route('reservations.index') }}"
           class="nav-item {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <span class="nav-icon">🗓️</span>
            <span>Réservations</span>
        </a>

        <a href="{{ route('reservations.create') }}"
           class="nav-item {{ request()->routeIs('reservations.create') ? 'active' : '' }}">
            <span class="nav-icon">➕</span>
            <span>Nouvelle réservation</span>
        </a>

        {{-- FINANCES --}}
        <div class="nav-section">Finances</div>

        <a href="{{ route('paiements.index') }}"
           class="nav-item {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <span class="nav-icon">💰</span>
            <span>Paiements</span>
        </a>

        <a href="{{ route('factures.index') }}"
           class="nav-item {{ request()->routeIs('factures.*') ? 'active' : '' }}">
            <span class="nav-icon">🧾</span>
            <span>Factures & Reçus</span>
        </a>

        {{-- GESTION --}}
        <div class="nav-section">Gestion</div>

        <a href="{{ route('equipes.index') }}"
           class="nav-item {{ request()->routeIs('equipes.*') ? 'active' : '' }}">
            <span class="nav-icon">👥</span>
            <span>Équipes</span>
        </a>

        <a href="{{ route('rapports') }}"
           class="nav-item {{ request()->routeIs('rapports') ? 'active' : '' }}">
            <span class="nav-icon">📈</span>
            <span>Rapports</span>
        </a>

        <a href="{{ route('parametres') }}"
           class="nav-item {{ request()->routeIs('parametres') ? 'active' : '' }}">
            <span class="nav-icon">⚙️</span>
            <span>Paramètres</span>
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
                🚪 Se déconnecter
            </button>
        </form>
    </div>

</div>
