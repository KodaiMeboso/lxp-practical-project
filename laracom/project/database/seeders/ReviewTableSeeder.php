<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reviews')->insert([
            [
                'product_id' => 1,
                'customer_id' => 1,
                'evaluation' => 5,
                'comment' => '素晴らしい商品でした。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'customer_id' => 1,
                'evaluation' => 4,
                'comment' => '期待通りの品質で満足しています。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
