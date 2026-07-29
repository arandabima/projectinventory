<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->isAdmin() || $borrowing->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUser();
    }

    public function update(User $user, Borrowing $borrowing): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Borrowing $borrowing): bool
    {
        return false;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
    
}
