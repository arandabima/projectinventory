<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class DashboardReportTest extends TestCase
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

    public function test_admin_dashboard_shows_borrowing_statistics(): void
    {
        $admin = $this->makeAdmin();

        Item::create([
            'sku' => 'SKU-001',
            'name' => 'Mouse',
            'unit' => 'pcs',
            'price' => 100000,
            'current_stock' => 5,
            'minimum_stock' => 1,
            'status' => 'available',
        ]);

        Item::create([
            'sku' => 'SKU-002',
            'name' => 'Keyboard',
            'unit' => 'pcs',
            'price' => 150000,
            'current_stock' => 0,
            'minimum_stock' => 1,
            'status' => 'borrowed',
        ]);

        Borrowing::create([
            'user_id' => $admin->id,
            'status' => 'pending',
            'borrow_date' => now()->subDay(),
            'return_date' => now()->addDays(2),
        ]);

        Borrowing::create([
            'user_id' => $admin->id,
            'status' => 'approved',
            'borrow_date' => now()->subDays(3),
            'return_date' => now()->addDays(1),
        ]);

        Borrowing::create([
            'user_id' => $admin->id,
            'status' => 'returned',
            'borrow_date' => now()->subDays(7),
            'return_date' => now()->subDays(2),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeTextInOrder(['Total borrowing', '3']);
        $response->assertSeeTextInOrder(['Borrowing pending', '1']);
        $response->assertSeeTextInOrder(['Borrowing aktif', '1']);
        $response->assertSeeTextInOrder(['Borrowing selesai', '1']);
    }

    public function test_user_dashboard_shows_personal_borrowing_counts(): void
    {
        $user = $this->makeUser();
        $otherUser = $this->makeUser('other', 'other@example.com');

        Borrowing::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'borrow_date' => now()->subDay(),
            'return_date' => now()->addDays(1),
        ]);

        Borrowing::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'borrow_date' => now()->subDays(2),
            'return_date' => now()->addDays(3),
        ]);

        Borrowing::create([
            'user_id' => $otherUser->id,
            'status' => 'pending',
            'borrow_date' => now()->subDay(),
            'return_date' => now()->addDays(1),
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertSeeTextInOrder(['Jumlah transaksi', '2']);
        $response->assertSeeTextInOrder(['Pending borrowing', '1']);
    }

    public function test_report_export_generates_borrowings_csv(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();

        Borrowing::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'borrow_date' => now()->subDays(2),
            'return_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'report_type' => 'borrowings',
            'generated_by' => 'admin',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv');

        if (method_exists($response, 'streamedContent')) {
            $content = $response->streamedContent();
            $this->assertStringContainsString('Tanggal', $content);
            $this->assertStringContainsString('Borrowing', $content);
        }
    }

    public function test_report_export_generates_borrowing_payment_csv(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'borrow_date' => now()->subDays(2),
            'return_date' => now()->addDays(2),
        ]);

        Payment::create([
            'borrowing_id' => $borrowing->id,
            'amount' => 500000,
            'status' => Payment::STATUS_CONFIRMED,
            'payment_method' => 'doku',
            'doku_reference_number' => 'BORR-1-REF',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'report_type' => 'payments',
            'generated_by' => 'admin',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv');

        if (method_exists($response, 'streamedContent')) {
            $content = $response->streamedContent();
            $this->assertStringContainsString('BORR-1-REF', $content);
            $this->assertStringNotContainsString('order_number', $content);
        }
    }
}
