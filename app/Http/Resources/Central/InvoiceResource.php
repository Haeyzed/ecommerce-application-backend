<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the invoice.
             * @var int $id
             * @example 942
             */
            'id'              => $this->id,

            /**
             * The identifier of the tenant being billed.
             * @var string $tenant_id
             * @example "tenant-xyz"
             */
            'tenant_id'       => $this->tenant_id,

            /**
             * The identifier of the related subscription, if applicable.
             * @var int|null $subscription_id
             * @example 45
             */
            'subscription_id' => $this->subscription_id,

            /**
             * The total amount of the invoice in minor units (e.g., cents).
             * @var int $amount_minor
             * @example 4999
             */
            'amount_minor'    => $this->amount_minor,

            /**
             * The ISO currency code of the invoice.
             * @var string $currency
             * @example "USD"
             */
            'currency'        => $this->currency,

            /**
             * A description of the charges on the invoice.
             * @var string|null $description
             * @example "Pro Plan - Monthly Subscription (April)"
             */
            'description'     => $this->description,

            /**
             * The current payment status of the invoice (e.g., paid, pending, failed).
             * @var string $status
             * @example "paid"
             */
            'status'          => $this->status,

            /**
             * The payment gateway or provider used (e.g., stripe, paystack).
             * @var string|null $provider
             * @example "stripe"
             */
            'provider'        => $this->provider,

            /**
             * The date and time when the invoice was successfully paid.
             * @var string|null $paid_at
             * @example "2026-04-01T10:00:00Z"
             */
            'paid_at'         => $this->paid_at,
        ];
    }
}
