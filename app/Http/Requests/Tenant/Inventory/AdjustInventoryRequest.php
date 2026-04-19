<?php

namespace App\Http\Requests\Tenant\Inventory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $qty_change The quantity changed (positive or negative).
 * @property string $reason The reason for the movement.
 * @property string|null $note Additional notes about the movement.
 */
class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qty_change' => ['required', 'integer'],
            'reason'     => ['required', 'string'],
            'note'       => ['nullable', 'string'],
        ];
    }
}
