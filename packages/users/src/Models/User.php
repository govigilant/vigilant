<?php

namespace Vigilant\Users\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Vigilant\Users\Database\Factories\UserFactory;
use Vigilant\Users\Notifications\VerifyEmail;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property ?Carbon $email_verified_at
 * @property string $password
 * @property string $two_factor_secret
 * @property string $two_factor_recovery_codes
 * @property ?Carbon $two_factor_confirmed_at
 * @property string $remember_token
 * @property ?int $current_team_id
 * @property ?string $profile_photo_path
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Team $currentTeam
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function sendEmailVerificationNotification(): void
    {
        if (ce()) {
            $this->markEmailAsVerified();

            return;
        }

        $this->notify(new VerifyEmail);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
