<?php

namespace App\Http\Requests\Tenant\Cart;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $code The unique coupon code to apply to the cart.
 */
class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'exists:coupons,code'],
        ];
    }
}
