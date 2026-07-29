<?php

namespace Tests\Unit;

use App\Models\Borrowing;
use App\Models\User;
use App\Policies\BorrowingPolicy;
use PHPUnit\Framework\TestCase;

class BorrowingPolicyTest extends TestCase
{
    public function test_user_can_view_own_borrowing(): void
    {
        $policy = new BorrowingPolicy();
        $user = new User();
        $user->id = 10;
        $user->role = 'user';

        $borrowing = new Borrowing();
        $borrowing->user_id = 10;

        $this->assertTrue($policy->view($user, $borrowing));
    }

    public function test_user_cannot_view_other_users_borrowing(): void
    {
        $policy = new BorrowingPolicy();
        $user = new User();
        $user->id = 10;
        $user->role = 'user';

        $borrowing = new Borrowing();
        $borrowing->user_id = 11;

        $this->assertFalse($policy->view($user, $borrowing));
    }

    public function test_user_cannot_update_borrowing_status(): void
    {
        $policy = new BorrowingPolicy();
        $user = new User();
        $user->role = 'user';

        $borrowing = new Borrowing();

        $this->assertFalse($policy->update($user, $borrowing));
    }

    public function test_admin_can_view_all_borrowings(): void
    {
        $policy = new BorrowingPolicy();
        $admin = new User();
        $admin->role = 'admin';

        $borrowing = new Borrowing();
        $borrowing->user_id = 999;

        $this->assertTrue($policy->view($admin, $borrowing));
    }

    public function test_admin_can_update_borrowing_through_manage_rule(): void
    {
        $policy = new BorrowingPolicy();
        $admin = new User();
        $admin->role = 'admin';

        $borrowing = new Borrowing();

        $this->assertTrue($policy->manage($admin));
        $this->assertTrue($policy->update($admin, $borrowing));
    }
}
