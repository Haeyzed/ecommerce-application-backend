<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The title of the training.
             * @var string $title
             * @example "Cybersecurity Awareness 2026"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The detailed description of the training.
             * @var string|null $description
             * @example "Annual mandatory security compliance training."
             */
            'description' => ['nullable', 'string'],

            /**
             * The start date and time of the training.
             * @var string|null $starts_at
             * @example "2026-06-01 09:00:00"
             */
            'starts_at' => ['nullable', 'date'],

            /**
             * The end date and time of the training.
             * @var string|null $ends_at
             * @example "2026-06-01 12:00:00"
             */
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            /**
             * The location of the training.
             * @var string|null $location
             * @example "Conference Room A"
             */
            'location' => ['nullable', 'string', 'max:128'],

            /**
             * Indicates whether the training is required.
             * @var bool|null $is_mandatory
             * @example true
             */
            'is_mandatory' => ['boolean'],
        ];
    }
}
