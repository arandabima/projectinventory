<?php

namespace Tests\Unit;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use PHPUnit\Framework\TestCase;

class BorrowingStatusTest extends TestCase
{
    public function test_borrowing_status_enum_is_cast(): void
    {
        $borrowing = new Borrowing();
        $borrowing->status = BorrowingStatus::pending;

        $this->assertInstanceOf(BorrowingStatus::class, $borrowing->status);
        $this->assertSame('pending', $borrowing->status->value);
    }
}
