<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'ATK' => 'Alat tulis kantor', 'IT' => 'Perangkat teknologi',
            'Elektronik' => 'Elektronik dan aksesoris', 'Rumah Tangga' => 'Kebutuhan rumah tangga',
        ])->mapWithKeys(fn ($description, $name) => [$name => Category::firstOrCreate(['name' => $name], ['description' => $description])]);

        $products = [
            ['ATK-001','Kertas A4 80gsm','ATK','rim',12,5,65000], ['ATK-002','Pulpen Gel Hitam','ATK','pcs',80,20,4500],
            ['ATK-003','Buku Catatan A5','ATK','pcs',45,10,18000], ['ATK-004','Stapler Mini','ATK','pcs',20,5,35000],
            ['IT-001','Keyboard USB','IT','pcs',15,4,145000], ['IT-002','Mouse Wireless','IT','pcs',25,5,120000],
            ['IT-003','Flashdisk 64GB','IT','pcs',30,8,95000], ['IT-004','Headset USB','IT','pcs',18,5,175000],
            ['ELK-001','Lampu LED 12 Watt','Elektronik','pcs',40,10,28000], ['ELK-002','Kabel HDMI 2 Meter','Elektronik','pcs',22,5,85000],
            ['ELK-003','Stop Kontak 4 Lubang','Elektronik','pcs',25,5,60000], ['ELK-004','Power Bank 10000mAh','Elektronik','pcs',14,4,210000],
            ['RT-001','Tisu Wajah 250 Lembar','Rumah Tangga','pcs',60,15,17000], ['RT-002','Sabun Cuci Tangan','Rumah Tangga','botol',35,10,22000],
            ['RT-003','Kantong Sampah 60x80','Rumah Tangga','pack',28,8,30000], ['RT-004','Dispenser Air 19 Liter','Rumah Tangga','pcs',8,2,350000],
        ];
        foreach ($products as [$sku, $name, $category, $unit, $stock, $minimum, $price]) {
            Item::updateOrCreate(['sku' => $sku], ['category_id' => $categories[$category]->id, 'name' => $name, 'unit' => $unit, 'current_stock' => $stock, 'minimum_stock' => $minimum, 'price' => $price, 'status' => 'available', 'location' => 'Gudang Utama']);
        }
    }
}
