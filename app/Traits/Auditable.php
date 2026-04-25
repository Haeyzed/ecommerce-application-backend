<?php

namespace App\Traits;

use OwenIt\Auditing\Auditable as AuditingAuditable;

/**
 * Trait Auditable
 *
 * Convenience wrapper trait for OwenIt's Auditable package.
 * Models using this must also implement the \OwenIt\Auditing\Contracts\Auditable interface.
 * * Note: Spatie's audit migrations should be published into both database/migrations
 * and database/migrations/tenant so audits land in the correct DB context.
 */
trait Auditable
{
    use AuditingAuditable;
}
