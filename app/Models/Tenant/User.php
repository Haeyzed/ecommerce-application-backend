<?php

namespace App\Models\Tenant;

use App\Notifications\Central\Auth\ResetPasswordNotification;
use App\Notifications\Central\Auth\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * Represents a registered user or store owner within the central application.
 *
 * @property int $id The unique identifier of the user.
 * @property string $name The full name of the user.
 * @property string $email The user's email address.
 * @property Carbon|null $email_verified_at Timestamp of when the email was verified.
 * @property string $password The hashed password of the user.
 * @property string|null $provider The OAuth provider used for registration (e.g., google, github).
 * @property string|null $provider_id The unique identifier provided by the OAuth service.
 * @property string|null $remember_token The token used for "remember me" functionality.
 * @property Carbon|null $created_at Timestamp of when the account was created.
 * @property Carbon|null $updated_at Timestamp of when the account was last updated.
 * @property Carbon|null $deleted_at Timestamp of when the account was soft deleted.
 *
 * @package App\Models\Central
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password' => 'hashed',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }
}
