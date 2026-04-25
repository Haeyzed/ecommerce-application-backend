<?php

namespace App\Enums\Tenant\HR;

enum LeaveType: string
{
    case SICK = 'sick';
    case VACATION = 'vacation';
    case MATERNITY = 'maternity';
    case UNPAID = 'unpaid';
    case OTHER = 'other';
}
