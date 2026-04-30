<?php

namespace App\Http\Requests\Tenant\Payment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $provider The payment provider (e.g., Stripe, PayPal).
 * @property string|null $provider_ref The transaction reference from the provider.
 * @property float|null $amount The payment amount.
 * @property string|null $status The current status of the payment.
 * @property array|null $meta Additional metadata from the provider.
 */
class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string'],
            'provider_ref' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,succeeded,failed,refunded'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
