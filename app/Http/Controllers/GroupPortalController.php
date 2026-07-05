<?php

namespace App\Http\Controllers;

use App\Models\ServiceGroup;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupPortalController extends Controller
{
    public function show(ServiceGroup $group, Request $request): View
    {
        $families = $group->families()
            ->with('brothers.ticket')
            ->orderBy('name')
            ->get();

        $selectedFamilyId = $request->integer('familia') ?: $families->first()?->id;
        $selectedBrotherId = $request->integer('irmao');
        $selectedFamily = $families->firstWhere('id', $selectedFamilyId);
        $brothers = $selectedFamily?->brothers?->sortBy('name')->values() ?? collect();
        $selectedBrother = $selectedBrotherId ? $brothers->firstWhere('id', $selectedBrotherId) : $brothers->first();
        $ticket = $selectedBrother?->ticket;

        return view('groups.portal', [
            'group' => $group,
            'families' => $families,
            'selectedFamily' => $selectedFamily,
            'selectedBrother' => $selectedBrother,
            'ticket' => $ticket,
        ]);
    }

    public function markSent(ServiceGroup $group, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->service_group_id === $group->id, 404);

        $ticket->markSent("grupo {$group->number}");

        return back()->with('status', 'Bilhete marcado como enviado.');
    }
}
