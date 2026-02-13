<?php

namespace App\Enums;

enum ScoreVerificationStatus: string
{
    case Unverified = 'unverified';
    case Pending = 'pending';
    case Verified = 'verified';
}
