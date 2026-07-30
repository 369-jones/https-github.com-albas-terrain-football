<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* ─── RESET ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8fafc; color: #0f172a; display: flex; min-height: 100vh; }

        /* ─── SIDEBAR ────────────────────────────────── */
        #sidebar {
            width: 260px; min-height: 100vh;
            background: #0f172a;
            display: flex; flex-direction: column;
            position: fixed; left: 0; top: 0; bottom: 0;
            z-index: 100;
        }
        .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo { font-size: 32px; }
        .sidebar-logo-img { width: 36px; height: 36px; flex-shrink: 0; }
        .sidebar-title { color: #fff; font-size: 15px; font-weight: 800; }
        .sidebar-sub { color: #64748b; font-size: 11px; margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
        .nav-section {
            padding: 8px 16px 4px;
            font-size: 10px; font-weight: 700;
            color: #475569; text-transform: uppercase; letter-spacing: 0.08em;
            margin-top: 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px; cursor: pointer;
            color: #cbd5e1; font-size: 13.5px; font-weight: 500;
            transition: all 0.15s; border-radius: 6px;
            margin: 1px 8px; text-decoration: none;
        }
        .nav-item:hover { background: #1e293b; color: #fff; }
        .nav-item.active { background: #991b1b; color: #fff; }
        .nav-icon { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .user-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; background: rgba(255,255,255,0.05);
            border-radius: 8px; margin-bottom: 8px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #b91c1c, #7f1d1d);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 15px;
            font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: 13px; font-weight: 700; }
        .user-email { color: #64748b; font-size: 11px; margin-top: 1px; }
        .btn-logout {
            width: 100%; padding: 9px;
            background: rgba(239,68,68,0.1);
            color: #ef4444; border: 1px solid rgba(239,68,68,0.2);
            border-radius: 7px; cursor: pointer;
            font-size: 13px; font-weight: 700;
            transition: all 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.2); }

        /* ─── MAIN ───────────────────────────────────── */
        #main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ─── TOPBAR ─────────────────────────────────── */
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 0 24px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
        .breadcrumb .current { color: #0f172a; font-weight: 700; }
        .breadcrumb .sep { color: #cbd5e1; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-date { font-size: 12px; color: #94a3b8; }
        .topbar-lang-select {
            border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;
            color: #334155; font-size: 13px; font-weight: 600; padding: 6px 10px;
            cursor: pointer;
        }
        .topbar-lang-select:hover { background: #f1f5f9; }
        .topbar-icon-btn {
            position: relative; display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 50%; background: #f8fafc;
            color: #64748b; font-size: 15px; transition: all 0.15s;
        }
        .topbar-icon-btn:hover { background: #fee2e2; color: #991b1b; }
        .topbar-icon-badge {
            position: absolute; top: -2px; right: -2px;
            background: #dc2626; color: #fff; border-radius: 20px;
            font-size: 10px; font-weight: 800; padding: 1px 5px; line-height: 1.4;
        }

        /* ─── CONTENT ────────────────────────────────── */
        .content { padding: 24px; flex: 1; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: #64748b; margin-bottom: 24px; }

        /* ─── CARDS ──────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border-radius: 12px; padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex; align-items: flex-start; justify-content: space-between;
            transition: all 0.2s; cursor: pointer;
        }
        .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .stat-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 30px; font-weight: 800; margin: 6px 0 4px; }
        .stat-sub { font-size: 12px; color: #94a3b8; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .card-blue { background: linear-gradient(135deg,#fef2f2,#fee2e2); border-color: #fca5a5; }
        .card-blue .stat-value { color: #b91c1c; }
        .card-blue .stat-icon { background: #dc2626; }
        .card-green { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border-color: #bbf7d0; }
        .card-green .stat-value { color: #15803d; }
        .card-green .stat-icon { background: #16a34a; }
        .card-orange { background: linear-gradient(135deg,#fff7ed,#fed7aa); border-color: #fdba74; }
        .card-orange .stat-value { color: #c2410c; }
        .card-orange .stat-icon { background: #ea580c; }
        .card-purple { background: linear-gradient(135deg,#f5f3ff,#ede9fe); border-color: #c4b5fd; }
        .card-purple .stat-value { color: #6d28d9; }
        .card-purple .stat-icon { background: #7c3aed; }
        .card-teal { background: linear-gradient(135deg,#f0fdfa,#ccfbf1); border-color: #99f6e4; }
        .card-teal .stat-value { color: #0f766e; }
        .card-teal .stat-icon { background: #0d9488; }
        .card-red { background: linear-gradient(135deg,#fef2f2,#fee2e2); border-color: #fca5a5; }
        .card-red .stat-value { color: #b91c1c; }
        .card-red .stat-icon { background: #dc2626; }

        /* ─── PANEL ──────────────────────────────────── */
        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); margin-bottom: 20px; }
        .panel-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .panel-title { font-size: 15px; font-weight: 700; }
        .panel-body { padding: 20px; }

        /* ─── TABLE ──────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th { background: #f8fafc; padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        .table-empty { text-align: center; padding: 40px; color: #94a3b8; font-size: 13px; }

        /* ─── BADGES ─────────────────────────────────── */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-blue   { background: #fee2e2; color: #991b1b; }
        .badge-orange { background: #fed7aa; color: #c2410c; }
        .badge-red    { background: #fee2e2; color: #b91c1c; }
        .badge-purple { background: #ede9fe; color: #6d28d9; }
        .badge-gray   { background: #f1f5f9; color: #475569; }

        /* ─── BUTTONS ────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg,#dc2626,#991b1b); color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,0.25); }
        .btn-primary:hover { opacity: 0.9; box-shadow: 0 4px 14px rgba(220,38,38,0.35); }
        .btn-success { background: linear-gradient(135deg,#16a34a,#15803d); color: #fff; }
        .btn-success:hover { opacity: 0.9; }
        .btn-danger  { background: linear-gradient(135deg,#dc2626,#b91c1c); color: #fff; }
        .btn-danger:hover  { opacity: 0.9; }
        .btn-outline { background: #fff; color: #0f172a; border: 1.5px solid #e2e8f0; }
        .btn-outline:hover { background: #f8fafc; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ─── FORMS ──────────────────────────────────── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid.cols-1 { grid-template-columns: 1fr; }
        .form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .field input, .field select, .field textarea {
            width: 100%; padding: 10px 13px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; color: #0f172a; background: #f8fafc;
            outline: none; transition: all 0.2s;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: #dc2626; background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }
        .field textarea { resize: vertical; min-height: 90px; }
        .field .error { color: #dc2626; font-size: 12px; margin-top: 4px; }

        /* ─── ALERT ──────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

        /* ─── FLEX UTILS ─────────────────────────────── */
        .flex-between { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .actions { display: flex; gap: 6px; }
    </style>
    @yield('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')

    {{-- MAIN --}}
    <div id="main">

        {{-- TOPBAR --}}
        <div class="topbar">
            <div class="breadcrumb">
                <span>{{ config('app.name') }}</span>
                <span class="sep">›</span>
                <span class="current">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="topbar-right">
                <select class="topbar-lang-select" aria-label="Langue" onchange="window.location.href = this.value">
                    @foreach (config('locales.supported') as $code => $lang)
                        <option value="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" @selected($code === app()->getLocale())>
                            {{ $lang['flag'] }} {{ strtoupper($code) }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" aria-label="Notifications">
                    <i class="fa-solid fa-bell"></i>
                    @php $topbarNotifCount = \App\Models\Notification::nonLues()->count(); @endphp
                    @if ($topbarNotifCount > 0)
                        <span class="topbar-icon-badge">{{ $topbarNotifCount }}</span>
                    @endif
                </a>
                <span class="topbar-date" id="topbarDate"></span>
            </div>
        </div>

        {{-- CONTENU --}}
        <div class="content">

            {{-- MESSAGES FLASH --}}
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>

    </div>

    <script>
        // Date dans la topbar
        const d = new Date();
        document.getElementById('topbarDate').textContent =
            d.toLocaleDateString('fr-FR', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
    </script>

    @yield('scripts')

    {{-- POLLING NOTIFICATIONS TEMPS RÉEL --}}
<div id="notif-toast" style="
    position:fixed;bottom:24px;right:24px;z-index:9999;
    display:flex;flex-direction:column;gap:10px;
    max-width:360px;
"></div>

<script>
    let lastCount = {{ \App\Models\Notification::nonLues()->count() }};

    function showToast(notif) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            background:${notif.background};
            border:1px solid ${notif.border};
            border-left:4px solid ${notif.couleur};
            border-radius:12px;padding:14px 16px;
            box-shadow:0 8px 24px rgba(0,0,0,0.15);
            animation:slideInRight 0.3s ease;
            cursor:pointer;
        `;
        toast.innerHTML = `
            <div style="display:flex;align-items:flex-start;gap:10px">
                <span style="font-size:20px;color:${notif.couleur}"><i class="fa-solid ${notif.icone}"></i></span>
                <div style="flex:1">
                    <div style="font-weight:700;font-size:13px;color:${notif.couleur}">
                        ${notif.titre}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px">
                        ${notif.message}
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:4px">
                        ${notif.temps}
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        style="background:none;border:none;cursor:pointer;
                               color:#94a3b8;font-size:16px;padding:0"><i class="fa-solid fa-xmark"></i></button>
            </div>
        `;
        if (notif.lien) {
            toast.addEventListener('click', () => window.location.href = notif.lien);
        }
        document.getElementById('notif-toast').appendChild(toast);

        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }

    // Mettre à jour le badge dans le sidebar
    function updateBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        }
    }

    // Polling toutes les 15 secondes
    async function checkNotifications() {
        try {
            const res  = await fetch('{{ route("notifications.api") }}');
            const data = await res.json();

            updateBadge(data.count);

            // Afficher toast si nouvelles notifs
            if (data.count > lastCount) {
                const nouvelles = data.notifications.slice(0, data.count - lastCount);
                nouvelles.forEach(n => showToast(n));
            }
            lastCount = data.count;
        } catch(e) {
            console.log('Polling notifications:', e);
        }
    }

    // Lancer le polling
    setInterval(checkNotifications, 15000);

    // Animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { opacity:0; transform:translateX(100%); }
            to   { opacity:1; transform:translateX(0); }
        }
    `;
    document.head.appendChild(style);
</script>
</body>
</html>
