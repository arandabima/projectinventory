<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isUser();
    }

    public function view(User $user, Chat $chat): bool
    {
        return $user->isAdmin() || $chat->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUser();
    }

    public function reply(User $user, Chat $chat): bool
    {
        return $user->isAdmin() || $chat->user_id === $user->id;
    }
}
