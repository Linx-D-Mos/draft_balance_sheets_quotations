<?php

declare(strict_types=1);

namespace App\Enums;

enum QuoteStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case APPROVED = 'approved';
    case CLOSED_BY_AMENDMENT = 'closed_by_amendment';
    case CANCELED = 'canceled';
}
