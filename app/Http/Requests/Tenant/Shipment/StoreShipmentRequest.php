<?php

namespace App\Http\Requests\Tenant\Shipment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $carrier The shipping carrier used.
 * @property string|null $tracking_number The tracking number for the shipment.
 */
class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'carrier'         => ['required', 'string'],
            'tracking_number' => ['nullable', 'string'],
        ];
    }
}
