<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Chat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class BorrowingAndChatTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    private function makeUser(string $username = 'user', string $email = 'user@example.com'): User
    {
        return User::factory()->create([
            'name' => ucfirst($username),
            'username' => $username,
            'email' => $email,
            'password' => 'password',
            'role' => 'user',
        ]);
    }

    public function test_user_can_create_borrowing_when_item_available(): void
    {
        $user = $this->makeUser();
        $item = Item::create([
            'sku' => 'SKU-001',
            'name' => 'Mouse',
            'unit' => 'pcs',
            'price' => 100000,
            'current_stock' => 5,
            'minimum_stock' => 1,
            'status' => 'available',
        ]);

        $response = $this->actingAs($user)->post(route('user.borrowings.store'), [
            'borrow_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(3)->toDateString(),
            'notes' => 'Dipinjam untuk kerja',
            'item_ids' => [$item->id],
            'quantities' => [1],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('borrowing_items', [
            'item_id' => $item->id,
            'quantity' => 1,
        ]);
    }

    public function test_user_cannot_borrow_item_that_is_borrowed(): void
    {
        $user = $this->makeUser();
        $item = Item::create([
            'sku' => 'SKU-002',
            'name' => 'Keyboard',
            'unit' => 'pcs',
            'price' => 150000,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($user)->post(route('user.borrowings.store'), [
            'borrow_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(3)->toDateString(),
            'notes' => null,
            'item_ids' => [$item->id],
            'quantities' => [1],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('borrowings', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_see_other_users_borrowing(): void
    {
        $owner = $this->makeUser('owner', 'owner@example.com');
        $other = $this->makeUser('other', 'other@example.com');
        $borrowing = Borrowing::create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'borrow_date' => now()->addDay(),
            'return_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($other)->get(route('user.borrowings.show', $borrowing));

        $response->assertForbidden();
    }

    public function test_admin_can_approve_and_return_borrowing(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $item = Item::create([
            'sku' => 'SKU-003',
            'name' => 'Monitor',
            'unit' => 'pcs',
            'price' => 1200000,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'status' => 'available',
        ]);

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'borrow_date' => now()->addDay(),
            'return_date' => now()->addDays(4),
        ]);
        $borrowing->items()->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => $item->price,
            'subtotal' => $item->price,
        ]);

        $approve = $this->actingAs($admin)->post(route('admin.borrowings.approve', $borrowing));
        $approve->assertRedirect();

        $item->refresh();
        $borrowing->refresh();

        $this->assertSame('approved', $borrowing->status->value);
        $this->assertSame('borrowed', $item->status->value);
        $this->assertSame(2, $item->current_stock);

        $returned = $this->actingAs($admin)->post(route('admin.borrowings.returned', $borrowing));
        $returned->assertRedirect();

        $item->refresh();
        $borrowing->refresh();

        $this->assertSame('returned', $borrowing->status->value);
        $this->assertSame('available', $item->status->value);
        $this->assertSame(3, $item->current_stock);
    }

    public function test_admin_cannot_approve_rejected_borrowing(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'status' => 'rejected',
            'borrow_date' => now()->addDay(),
            'return_date' => now()->addDays(4),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.borrowings.approve', $borrowing));

        $response->assertSessionHasErrors(['status']);
        $borrowing->refresh();

        $this->assertSame('rejected', $borrowing->status->value);
    }

    public function test_admin_cannot_return_pending_borrowing(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'borrow_date' => now()->addDay(),
            'return_date' => now()->addDays(4),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.borrowings.returned', $borrowing));

        $response->assertSessionHasErrors(['status']);
        $borrowing->refresh();

        $this->assertSame('pending', $borrowing->status->value);
    }

    public function test_user_can_send_chat_and_cannot_open_other_chat(): void
    {
        $user = $this->makeUser();
        $admin = $this->makeAdmin();
        $otherUser = $this->makeUser('other', 'other@example.com');

        $chat = Chat::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);

        $response = $this->actingAs($user)->post(route('user.chat.store', $chat), [
            'message' => 'Halo admin',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'receiver_id' => $admin->id,
            'message' => 'Halo admin',
        ]);

        $otherResponse = $this->actingAs($otherUser)->get(route('user.chat.index'));
        $otherResponse->assertOk();

        $forbidden = $this->actingAs($otherUser)->post(route('user.chat.store', $chat), [
            'message' => 'Tidak boleh',
        ]);

        $forbidden->assertForbidden();
    }

    public function test_admin_can_view_all_chats(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        Chat::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.chat.index'));

        $response->assertOk();
        $response->assertSee($user->name);
    }
}
