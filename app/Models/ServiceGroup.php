<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceGroup extends Model
{
    protected $fillable = [
        'number',
        'name',
        'responsible_name',
        'responsible_phone',
        'public_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (ServiceGroup $group): void {
            $group->public_token ??= Str::random(48);
        });
    }

    public function families(): HasMany
    {
        return $this->hasMany(TicketFamily::class);
    }

    public function brothers(): HasMany
    {
        return $this->hasMany(Brother::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }
}
