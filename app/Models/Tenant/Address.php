<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Address
 *
 * Represents a physical address associated with a customer.
 *
 * @property int $id The unique identifier of the address.
 * @property int $customer_id The foreign key referencing the customer.
 * @property string $type The type of address (e.g., billing, shipping).
 * @property string $name The name associated with the address.
 * @property string $line1 Address line 1.
 * @property string|null $line2 Address line 2.
 * @property string $city The city of the address.
 * @property string $state The state or province.
 * @property string $postal_code The postal or ZIP code.
 * @property string $country The country code or name.
 * @property string|null $phone A contact phone number for the address.
 * @property bool $is_default Indicates if this is the customer's default address.
 * @property Carbon|null $created_at Timestamp of when the address was created.
 * @property Carbon|null $updated_at Timestamp of when the address was last updated.
 *
 * @property-read Customer $customer The customer this address belongs to.
 *
 * @package App\Models\Tenant
 */
class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'type',
        'name',
        'line1',
        'line2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the customer that the address belongs to.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
