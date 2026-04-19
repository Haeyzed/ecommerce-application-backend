<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Customer
 *
 * Represents a registered customer purchasing from a tenant's store.
 *
 * @property int $id The unique identifier of the customer.
 * @property string $name The full name of the customer.
 * @property string $email The customer's email address.
 * @property string $password The hashed password of the customer.
 * @property string|null $remember_token The token used for "remember me" functionality.
 * @property Carbon|null $created_at Timestamp of when the account was created.
 * @property Carbon|null $updated_at Timestamp of when the account was last updated.
 *
 * @property-read Collection|Order[] $orders The orders placed by this customer.
 *
 * @package App\Models\Tenant
 */
class Customer extends Authenticatable
{
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Get the orders placed by the customer.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
