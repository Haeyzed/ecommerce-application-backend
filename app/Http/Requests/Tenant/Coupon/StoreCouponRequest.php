<?php

namespace App\Http\Requests\Tenant\Coupon;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $code The unique coupon code.
 * @property string $type The type of discount (percent or fixed).
 * @property string $value The discount value.
 * @property string|null $min_subtotal The minimum subtotal required to apply the coupon.
 * @property int|null $max_uses The maximum number of times the coupon can be used.
 * @property string|null $starts_at Timestamp of when the coupon becomes active.
 * @property string|null $ends_at Timestamp of when the coupon expires.
 * @property bool|null $is_active Indicates if the coupon is active.
 */
class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:32'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ];
    }
}
