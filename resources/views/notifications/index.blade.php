@extends('layouts.main')
@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">🔔 Notifications</div>
        <div class="page-subtitle">
            {{ $non_lues }} notification(s) non lue(s)
        </div>
    </div>
    <div style="display:flex;gap:10px">
        @if($non_lues > 0)
        <form method="POST" action="{{ route('notifications.lire-tout') }}">
            @csrf
            <button class="btn btn-primary">✅ Tout marquer comme lu</button>
        </form>
        @endif
        <form method="POST" action="{{ route('notifications.destroy-lues') }}"
              onsubmit="return confirm('Supprimer toutes les notifications lues ?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">🗑 Supprimer les lues</button>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel-body" style="padding:0">
        @forelse($notifications as $n)
        <div style="
            display:flex;align-items:flex-start;gap:14px;
            padding:16px 20px;
            border-bottom:1px solid #f1f5f9;
            background:{{ $n->lue ? '#fff' : $n->background() }};
            border-left:4px solid {{ $n->lue ? '#e2e8f0' : $n->border() }};
            transition:all 0.2s;
        ">
            {{-- ICÔNE --}}
            <div style="
                width:40px;height:40px;border-radius:10px;
                background:{{ $n->background() }};border:1px solid {{ $n->border() }};
                display:flex;align-items:center;justify-content:center;
                font-size:18px;flex-shrink:0;
            ">
                {{ $n->icone() }}
            </div>

            {{-- CONTENU --}}
            <div style="flex:1">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px">
                    <span style="font-weight:700;font-size:14px;color:{{ $n->couleur() }}">
                        {{ $n->titre }}
                    </span>
                    @if(!$n->lue)
                    <span style="
                        width:8px;height:8px;border-radius:50%;
                        background:#2563eb;display:inline-block;
                    "></span>
                    @endif
                    <span class="badge badge-gray" style="font-size:10px">
                        {{ $n->categorie }}
                    </span>
                </div>
                <div style="font-size:13px;color:#64748b;margin-bottom:4px">
                    {{ $n->message }}
                </div>
                <div style="font-size:11px;color:#94a3b8">
                    🕐 {{ $n->created_at->diffForHumans() }}
                    @if($n->lue && $n->lue_at)
                    · Lu le {{ $n->lue_at->format('d/m/Y à H:i') }}
                    @endif
                </div>
            </div>

            {{-- ACTIONS --}}
            <div style="display:flex;gap:6px;flex-shrink:0">
                @if(!$n->lue)
                <form method="POST" action="{{ route('notifications.lire', $n) }}">
                    @csrf
                    <button class="btn btn-outline btn-sm" title="Marquer comme lu">✓</button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $n) }}"
                      onsubmit="return confirm('Supprimer ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" title="Supprimer">🗑</button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px;color:#94a3b8">
            <div style="font-size:48px;margin-bottom:12px">🔔</div>
            <div style="font-size:16px;font-weight:700;margin-bottom:6px">
                Aucune notification
            </div>
            <div style="font-size:13px">
                Les notifications apparaissent automatiquement lors des actions
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- PAGINATION --}}
<div style="margin-top:16px">
    {{ $notifications->links() }}
</div>

@endsection
