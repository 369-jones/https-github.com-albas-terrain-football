@extends('layouts.main')
@section('title', 'Équipes')
@section('breadcrumb', 'Équipes')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">👥 Équipes</div>
        <div class="page-subtitle">Gérez les équipes universitaires inscrites</div>
    </div>
    <a href="{{ route('equipes.create') }}" class="btn btn-primary">+ Nouvelle équipe</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Liste des équipes ({{ $equipes->count() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Équipe</th>
                    <th>Responsable</th>
                    <th>Contact</th>
                    <th>Faculté</th>
                    <th>Matchs</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipes as $equipe)
                <tr>
                    <td style="color:#94a3b8">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700">{{ $equipe->nom }}</div>
                        @if($equipe->email)
                        <div style="font-size:12px;color:#64748b">{{ $equipe->email }}</div>
                        @endif
                    </td>
                    <td>{{ $equipe->responsable }}</td>
                    <td>{{ $equipe->contact }}</td>
                    <td>{{ $equipe->faculte ?? '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $equipe->reservations_count }} matchs</span>
                    </td>
                    <td>
                        @if($equipe->statut === 'actif')
                            <span class="badge badge-green">Actif</span>
                        @else
                            <span class="badge badge-red">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('equipes.show', $equipe) }}"
                               class="btn btn-outline btn-sm">👁 Voir</a>
                            <a href="{{ route('equipes.edit', $equipe) }}"
                               class="btn btn-primary btn-sm">✏ Modifier</a>
                            <form method="POST" action="{{ route('equipes.destroy', $equipe) }}"
                                  onsubmit="return confirm('Supprimer cette équipe ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="table-empty">
                        Aucune équipe inscrite —
                        <a href="{{ route('equipes.create') }}" style="color:#2563eb">Créer une équipe</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
