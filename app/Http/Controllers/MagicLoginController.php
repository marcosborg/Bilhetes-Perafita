<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MagicLoginController extends Controller
{
    public function redirect(User $user, string $token): RedirectResponse
    {
        return redirect()->route('magic-portal', [$user, $token]);
    }

    public function show(Request $request, User $user, string $token): View
    {
        abort_unless($user->hasValidMagicLoginToken($token), 403);
        abort_unless($user->service_group_id !== null, 404);

        $group = $user->serviceGroup;
        $tickets = $group->tickets()
            ->with(['brother', 'family'])
            ->whereNotNull('brother_id')
            ->orderBy('status')
            ->orderBy('pdf_filename')
            ->get();

        return view('magic.portal', [
            'user' => $user,
            'token' => $token,
            'group' => $group,
            'tickets' => $tickets,
            'selectedTicket' => $request->integer('bilhete')
                ? $tickets->firstWhere('id', $request->integer('bilhete'))
                : $tickets->first(),
        ]);
    }

    public function markSent(User $user, string $token, Ticket $ticket): RedirectResponse
    {
        abort_unless($user->hasValidMagicLoginToken($token), 403);
        abort_unless($user->service_group_id !== null && $ticket->service_group_id === $user->service_group_id, 404);

        $ticket->markSent($user->name);

        return back()->with('status', 'Bilhete marcado como enviado.');
    }
}
