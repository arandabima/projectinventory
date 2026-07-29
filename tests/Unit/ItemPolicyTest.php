<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\User;
use App\Policies\ItemPolicy;
use PHPUnit\Framework\TestCase;

class ItemPolicyTest extends TestCase
{
    public function test_user_can_see_item(): void
    {
        $policy = new ItemPolicy();
        $user = new User();
        $user->role = 'user';

        $item = new Item();

        $this->assertTrue($policy->view($user, $item));
    }

    public function test_user_cannot_create_item(): void
    {
        $policy = new ItemPolicy();
        $user = new User();
        $user->role = 'user';

        $this->assertFalse($policy->create($user));
    }

    public function test_user_cannot_update_item(): void
    {
        $policy = new ItemPolicy();
        $user = new User();
        $user->role = 'user';

        $item = new Item();

        $this->assertFalse($policy->update($user, $item));
    }

    public function test_admin_can_create_item(): void
    {
        $policy = new ItemPolicy();
        $admin = new User();
        $admin->role = 'admin';

        $this->assertTrue($policy->create($admin));
    }

    public function test_admin_can_update_item(): void
    {
        $policy = new ItemPolicy();
        $admin = new User();
        $admin->role = 'admin';
        $item = new Item();

        $this->assertTrue($policy->update($admin, $item));
    }

    public function test_admin_can_delete_item(): void
    {
        $policy = new ItemPolicy();
        $admin = new User();
        $admin->role = 'admin';
        $item = new Item();

        $this->assertTrue($policy->delete($admin, $item));
    }
}
