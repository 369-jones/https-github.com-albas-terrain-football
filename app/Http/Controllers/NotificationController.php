<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    // Liste toutes les notifications
    public function index()
    {
        $notifications = Notification::recentes()->paginate(15);
        $non_lues = Notification::nonLues()->count();

        return view('notifications.index', compact('notifications', 'non_lues'));
    }

    // Marquer une notification comme lue
    public function lire(Notification $notification)
    {
        $notification->marquerLue();

        if ($notification->lien) {
            return redirect($notification->lien);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    // Marquer toutes comme lues
    public function lireTout()
    {
        Notification::nonLues()->update([
            'lue' => true,
            'lue_at' => now(),
        ]);

        return back()->with('success', 'Toutes les notifications ont été lues.');
    }

    // Supprimer une notification
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    // Supprimer toutes les notifications lues
    public function destroyLues()
    {
        Notification::where('lue', true)->delete();

        return back()->with('success', 'Notifications lues supprimées.');
    }

    // API JSON — pour le polling temps réel
    public function api()
    {
        $notifications = Notification::nonLues()
            ->recentes()
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'titre' => $n->titre,
                'message' => $n->message,
                'type' => $n->type,
                'icone' => $n->icone(),
                'couleur' => $n->couleur(),
                'background' => $n->background(),
                'border' => $n->border(),
                'lien' => $n->lien,
                'temps' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }
}
