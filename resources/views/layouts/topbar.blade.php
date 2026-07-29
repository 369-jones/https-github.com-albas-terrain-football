<div class="topbar">
    <div class="breadcrumb">
        <span>Terrain Football</span>
        <span class="sep">›</span>
        <span class="active">@yield('page_title', 'Dashboard')</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-date">
            {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </span>
    </div>
</div>
