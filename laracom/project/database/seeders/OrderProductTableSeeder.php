<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orderProductRelations = [
            ['order_id' => 1, 'product_id' => 1, 'quantity' => 2],
            ['order_id' => 1, 'product_id' => 2, 'quantity' => 1],
            ['order_id' => 2, 'product_id' => 3, 'quantity' => 1],
            ['order_id' => 2, 'product_id' => 4, 'quantity' => 2],
            // 他のデータを追加
        ];

        foreach ($orderProductRelations as $relation) {
            DB::table('order_product')->insert($relation);
        }
    }
}
