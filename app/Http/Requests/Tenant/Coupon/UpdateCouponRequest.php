<?php

namespace App\Http\Requests\Tenant\Coupon;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $value The discount value.
 * @property int|null $max_uses The maximum number of times the coupon can be used.
 * @property string|null $ends_at Timestamp of when the coupon expires.
 * @property bool|null $is_active Indicates if the coupon is active.
 */
class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'ends_at' => ['sometimes', 'nullable', 'date'],
            'max_uses' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }
}
