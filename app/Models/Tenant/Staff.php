<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Staff
 *
 * Represents a storefront Staff profile linked to an authenticatable User.
 *
 * @property int $id The unique identifier of the Staff profile.
 * @property int $user_id The foreign key referencing the base User account.
 * @property string|null $phone The Staff's contact phone number.
 * @property string|null $avatar_url The URL to the Staff's profile picture.
 * @property Carbon|null $date_of_birth The Staff's date of birth.
 * @property string|null $gender The Staff's gender.
 * @property string $currency The Staff's preferred currency code (e.g., USD).
 * @property string $locale The Staff's preferred language/locale (e.g., en).
 * @property string|null $notes Internal notes about the Staff (admin only).
 * @property bool $is_active Indicates if the Staff profile is active and permitted to shop.
 * @property Carbon|null $created_at Timestamp of when the profile was created.
 * @property Carbon|null $updated_at Timestamp of when the profile was last updated.
 * @property Carbon|null $deleted_at Timestamp of when the profile was soft deleted.
 * @property-read User $user The base user account for this Staff.
 * @property-read Collection|Order[] $orders The orders placed by this Staff.
 * @property-read Collection|Address[] $addresses The addresses saved by this Staff.
 */
class Staff extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'avatar_path',
        'avatar_url',
        'date_of_birth',
        'gender',
        'currency',
        'locale',
        'notes',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the authenticatable user associated with this Staff profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders placed by the Staff.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses saved by the Staff.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
