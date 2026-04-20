<?php

namespace App\Models\Tenant;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Coupon
 *
 * Represents a discount code that can be applied to a cart or order.
 *
 * @property int $id The unique identifier of the coupon.
 * @property string $code The unique code used to apply the coupon.
 * @property string $type The type of discount (e.g., fixed, percent).
 * @property string $value The discount value.
 * @property string|null $min_subtotal The minimum subtotal required to use the coupon.
 * @property int|null $max_uses The maximum number of times the coupon can be used.
 * @property int $used_count The number of times the coupon has been used.
 * @property Carbon|null $starts_at Timestamp of when the coupon becomes active.
 * @property Carbon|null $ends_at Timestamp of when the coupon expires.
 * @property bool $is_active Indicates if the coupon is active.
 * @property Carbon|null $created_at Timestamp of when the coupon was created.
 * @property Carbon|null $updated_at Timestamp of when the coupon was last updated.
 *
 * @package App\Models\Tenant
 */
class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_subtotal',
        'max_uses',
        'used_count',
        'starts_at',
        'ends_at',
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
            'value' => 'decimal:2',
            'min_subtotal' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if the coupon is redeemable based on subtotal and current time.
     *
     * @param float $subtotal The subtotal of the cart or order.
     * @param DateTimeInterface|null $now The current time (defaults to now).
     * @return bool
     */
    public function isRedeemable(float $subtotal, DateTimeInterface $now = null): bool
    {
        $now ??= now();
        if (!$this->is_active) return false;
        if ($this->starts_at && $now < $this->starts_at) return false;
        if ($this->ends_at && $now > $this->ends_at) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($this->min_subtotal !== null && $subtotal < (float) $this->min_subtotal) return false;

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     *
     * @param float $subtotal The subtotal to calculate the discount against.
     * @return float
     */
    public function discountFor(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ((float) $this->value / 100), 2);
        }

        return min((float) $this->value, $subtotal);
    }
}
