<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Customer
 *
 * Represents a storefront customer profile linked to an authenticatable User.
 *
 * @property int $id The unique identifier of the customer profile.
 * @property int $user_id The foreign key referencing the base User account.
 * @property string|null $phone The customer's contact phone number.
 * @property string|null $avatar_url The URL to the customer's profile picture.
 * @property Carbon|null $date_of_birth The customer's date of birth.
 * @property string|null $gender The customer's gender.
 * @property string $currency The customer's preferred currency code (e.g., USD).
 * @property string $locale The customer's preferred language/locale (e.g., en).
 * @property string|null $notes Internal notes about the customer (admin only).
 * @property bool $is_active Indicates if the customer profile is active and permitted to shop.
 * @property Carbon|null $created_at Timestamp of when the profile was created.
 * @property Carbon|null $updated_at Timestamp of when the profile was last updated.
 * @property Carbon|null $deleted_at Timestamp of when the profile was soft deleted.
 * @property-read User $user The base user account for this customer.
 * @property-read Collection|Order[] $orders The orders placed by this customer.
 * @property-read Collection|Address[] $addresses The addresses saved by this customer.
 */
class Customer extends Model
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
     * Get the authenticatable user associated with this customer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders placed by the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses saved by the customer.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
