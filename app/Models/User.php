<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'responsibility',
        'service_group_id',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'magic_login_expires_at' => 'datetime',
            'magic_login_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class);
    }

    public function shouldDefaultToServiceGroup(): bool
    {
        return $this->service_group_id !== null
            && in_array($this->responsibility, ['Superintendente de grupo', 'Ajudante'], true);
    }

    public function generateMagicLoginToken(): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'magic_login_token_hash' => hash('sha256', $token),
            'magic_login_expires_at' => now()->addDays(7),
            'magic_login_sent_at' => now(),
        ])->save();

        return $token;
    }

    public function whatsappPhone(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '9')) {
            return '351'.$digits;
        }

        return $digits;
    }

    public function magicLoginWhatsappText(string $loginUrl): string
    {
        $expiresAt = $this->magic_login_expires_at?->format('d/m/Y H:i');

        return "Olá {$this->name}, segue o teu link de acesso aos bilhetes do grupo. O link é válido até {$expiresAt}: {$loginUrl}";
    }

    public function hasValidMagicLoginToken(string $token): bool
    {
        return $this->magic_login_token_hash !== null
            && $this->magic_login_expires_at?->isFuture()
            && hash_equals($this->magic_login_token_hash, hash('sha256', $token));
    }
}
