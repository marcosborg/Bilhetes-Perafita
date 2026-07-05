<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketImport extends Model
{
    protected $fillable = [
        'excel_path',
        'zip_path',
        'zip_pdf_count',
        'mapped_ticket_count',
        'missing_pdf_count',
        'unmapped_pdf_count',
        'warnings',
        'finished_at',
    ];

    protected $casts = [
        'warnings' => 'array',
        'finished_at' => 'datetime',
    ];
}
