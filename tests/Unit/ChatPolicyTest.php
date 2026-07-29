<?php

namespace Tests\Unit;

use App\Models\Chat;
use App\Models\User;
use App\Policies\ChatPolicy;
use PHPUnit\Framework\TestCase;

class ChatPolicyTest extends TestCase
{
    public function test_user_can_access_own_chat(): void
    {
        $policy = new ChatPolicy();
        $user = new User();
        $user->id = 5;
        $user->role = 'user';

        $chat = new Chat();
        $chat->user_id = 5;

        $this->assertTrue($policy->view($user, $chat));
    }

    public function test_user_cannot_access_other_users_chat(): void
    {
        $policy = new ChatPolicy();
        $user = new User();
        $user->id = 5;
        $user->role = 'user';

        $chat = new Chat();
        $chat->user_id = 6;

        $this->assertFalse($policy->view($user, $chat));
    }

    public function test_admin_can_access_all_chats(): void
    {
        $policy = new ChatPolicy();
        $admin = new User();
        $admin->role = 'admin';

        $chat = new Chat();
        $chat->user_id = 999;

        $this->assertTrue($policy->view($admin, $chat));
    }
}
