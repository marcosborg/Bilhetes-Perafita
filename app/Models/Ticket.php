<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_SENT = 'sent';
    public const STATUS_PROBLEM = 'problem';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_ASSIGNED => 'Atribuído',
        self::STATUS_SENT => 'Enviado',
        self::STATUS_PROBLEM => 'Problema',
    ];

    protected $fillable = [
        'service_group_id',
        'ticket_family_id',
        'brother_id',
        'pdf_filename',
        'pdf_path',
        'internal_code',
        'public_token',
        'status',
        'sent_at',
        'source_row',
        'notes',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'source_row' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket): void {
            $ticket->public_token ??= Str::random(48);
        });
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(TicketFamily::class, 'ticket_family_id');
    }

    public function brother(): BelongsTo
    {
        return $this->belongsTo(Brother::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }

    public function markSent(string $actor = 'responsavel'): void
    {
        $this->forceFill([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ])->save();

        $this->events()->create([
            'service_group_id' => $this->service_group_id,
            'type' => 'sent',
            'actor' => $actor,
            'message' => 'Bilhete marcado como enviado.',
        ]);
    }

    public function whatsappText(): string
    {
        $name = $this->brother?->name ?: 'Irmão';
        $url = route('tickets.download', $this->public_token);

        return "Olá {$name}, segue o teu bilhete para o congresso: {$url}";
    }
}
