@extends('layouts.main')
@section('title', 'Aide')
@section('breadcrumb', 'Aide')

@section('content')

<div class="page-title"><i class="fa-solid fa-circle-question"></i> Aide</div>
<div class="page-subtitle">Questions fréquentes et contact support</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-list-check"></i> Questions fréquentes</div>
        </div>
        <div class="panel-body">
            <div style="display:flex;flex-direction:column;gap:20px">
                <div>
                    <p style="font-weight:700;margin-bottom:4px">Comment ajouter une nouvelle réservation ?</p>
                    <p style="color:#64748b;font-size:14px">Allez dans <strong>Réservations &rarr; Nouvelle réservation</strong>, choisissez l'équipe, la date et le créneau, puis enregistrez.</p>
                </div>
                <div>
                    <p style="font-weight:700;margin-bottom:4px">Comment marquer un paiement comme reçu ?</p>
                    <p style="color:#64748b;font-size:14px">Ouvrez la réservation concernée depuis <strong>Paiements</strong>, puis mettez à jour son statut.</p>
                </div>
                <div>
                    <p style="font-weight:700;margin-bottom:4px">Qui peut gérer les stades sur la plateforme publique ?</p>
                    <p style="color:#64748b;font-size:14px">Seul l'administrateur attribue un responsable à chaque stade, depuis <strong>Gestionnaires de stade</strong> sur le tableau de bord propriétaire.</p>
                </div>
                <div>
                    <p style="font-weight:700;margin-bottom:4px">Comment générer une facture PDF ?</p>
                    <p style="color:#64748b;font-size:14px">Depuis <strong>Factures & Reçus</strong>, ouvrez la facture puis cliquez sur <strong>Télécharger le PDF</strong>.</p>
                </div>
                <div>
                    <p style="font-weight:700;margin-bottom:4px">J'ai oublié mon mot de passe, que faire ?</p>
                    <p style="color:#64748b;font-size:14px">Seul l'administrateur peut réinitialiser un mot de passe. Contactez-le via les coordonnées ci-contre.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-headset"></i> Besoin d'aide supplémentaire ?</div>
        </div>
        <div class="panel-body">
            <p style="color:#64748b;font-size:14px;margin-bottom:16px">Notre équipe support est disponible pour toute question technique ou administrative.</p>
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700"><i class="fa-solid fa-envelope"></i> Email</td>
                    <td style="padding:12px 0"><a href="mailto:support@terrainfoot.com">support@terrainfoot.com</a></td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700"><i class="fa-solid fa-phone"></i> Téléphone</td>
                    <td style="padding:12px 0">+243 800 000 000</td>
                </tr>
                <tr>
                    <td style="padding:12px 0;color:#64748b;font-weight:700"><i class="fa-solid fa-clock"></i> Disponibilité</td>
                    <td style="padding:12px 0">Lundi–Samedi, 8h–18h (heure de Kinshasa)</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
