<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class TicketDownloadController extends Controller
{
    public function show(string $token): Response
    {
        $ticket = Ticket::where('public_token', $token)->firstOrFail();

        abort_unless($ticket->pdf_path && Storage::disk('local')->exists($ticket->pdf_path), 404);

        $ticket->events()->create([
            'service_group_id' => $ticket->service_group_id,
            'type' => 'viewed',
            'actor' => 'public-link',
            'message' => 'PDF aberto por link seguro.',
        ]);

        return response(Storage::disk('local')->get($ticket->pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$ticket->pdf_filename.'"',
        ]);
    }
}
