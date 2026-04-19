<?php

namespace App\Http\Requests\Central\Domain;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $domain The custom domain to attach to the tenant. @example shop.acme.com
 */
class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain' => [
                'required', 'string', 'max:253',
                'regex:/^(?!-)[A-Za-z0-9-]{1,63}(?<!-)(\.[A-Za-z0-9-]{1,63})+$/',
                Rule::unique('domains', 'domain'),
            ],
        ];
    }
}
