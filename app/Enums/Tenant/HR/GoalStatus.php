<?php

namespace App\Enums\Tenant\HR;

enum GoalStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case MISSED = 'missed';
}
