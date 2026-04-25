<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
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
             * The updated title of the training.
             * @var string|null $title
             * @example "Updated Cybersecurity Awareness"
             */
            'title' => ['sometimes', 'string', 'max:255'],

            /**
             * The updated description of the training.
             * @var string|null $description
             * @example "Updated compliance requirements for 2026."
             */
            'description' => ['sometimes', 'nullable', 'string'],

            /**
             * The updated start date and time.
             * @var string|null $starts_at
             * @example "2026-06-05 09:00:00"
             */
            'starts_at' => ['sometimes', 'nullable', 'date'],

            /**
             * The updated end date and time.
             * @var string|null $ends_at
             * @example "2026-06-05 12:00:00"
             */
            'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],

            /**
             * The updated location of the training.
             * @var string|null $location
             * @example "Main Hall"
             */
            'location' => ['sometimes', 'nullable', 'string', 'max:128'],

            /**
             * Indicates whether the training is required.
             * @var bool|null $is_mandatory
             * @example false
             */
            'is_mandatory' => ['sometimes', 'boolean'],
        ];
    }
}
