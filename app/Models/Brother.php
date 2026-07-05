<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Brother extends Model
{
    protected $fillable = [
        'service_group_id',
        'ticket_family_id',
        'name',
        'is_under_12',
        'is_over_75',
        'has_locomotion_need',
        'has_mobility_need',
        'normal_ticket',
        'andante',
        'distico',
        'source_row',
    ];

    protected $casts = [
        'is_under_12' => 'boolean',
        'is_over_75' => 'boolean',
        'has_locomotion_need' => 'boolean',
        'has_mobility_need' => 'boolean',
        'normal_ticket' => 'boolean',
        'andante' => 'boolean',
        'distico' => 'boolean',
        'source_row' => 'array',
    ];

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(TicketFamily::class, 'ticket_family_id');
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
