<?php

namespace App\Policies;

use App\Models\Attempt;
use App\Models\User;

class AttemptPolicy
{
    public function view(User $user, Attempt $attempt): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->isTenantAdmin()) return $attempt->user->tenant_id === $user->tenant_id;
        return $attempt->user_id === $user->id;
    }
}
