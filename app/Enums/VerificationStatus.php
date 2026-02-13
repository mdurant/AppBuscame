<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case RegisteredPendingEmail = 'registered_pending_email';
    case EmailVerifiedPendingOtp = 'email_verified_pending_otp';
    case FullyVerified = 'fully_verified';
}
