<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdraw;

class WithdrawPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'instructor';
    }

    public function create(User $user): bool
    {
        return $user->role === 'instructor';
    }

    public function view(User $user, Withdraw $withdraw): bool
    {
        return $user->role === 'instructor' && $withdraw->user_id === $user->id;
    }
}
