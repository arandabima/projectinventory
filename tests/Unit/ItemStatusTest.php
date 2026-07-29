<?php

namespace Tests\Unit;

use App\Enums\ItemStatus;
use App\Models\Item;
use PHPUnit\Framework\TestCase;

class ItemStatusTest extends TestCase
{
    public function test_item_status_enum_is_cast_for_available_items(): void
    {
        $item = new Item();
        $item->status = ItemStatus::available;

        $this->assertInstanceOf(ItemStatus::class, $item->status);
        $this->assertSame('available', $item->status->value);
    }

    public function test_item_status_enum_is_cast_for_borrowed_items(): void
    {
        $item = new Item();
        $item->status = ItemStatus::borrowed;

        $this->assertInstanceOf(ItemStatus::class, $item->status);
        $this->assertSame('borrowed', $item->status->value);
    }
}
