<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case PendingPayment = 'pending_payment';
    case Published = 'published';
    case Paused = 'paused';
    case Archived = 'archived';
}
