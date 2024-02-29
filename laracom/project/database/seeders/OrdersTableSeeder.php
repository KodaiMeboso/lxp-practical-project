<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfOrders = 10; // 作成したい注文の数

        for ($i = 0; $i < $numberOfOrders; $i++) {
            DB::table('orders')->insert([
                'reference' => Str::random(10),
                'courier_id' => 1,
                'customer_id' => rand(1, 5),
                'address_id' => rand(1, 5),
                'order_status_id' => rand(1, 3),
                'payment' => 'credit_card',
                'discounts' => 0.00,
                'total_products' => rand(50, 200),
                'tax' => 20.00,
                'total' => rand(70, 220),
                'total_paid' => rand(70, 220),
                'invoice' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
