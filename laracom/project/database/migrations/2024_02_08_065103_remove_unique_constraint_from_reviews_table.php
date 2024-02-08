<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // 外部キー制約を一時的に削除
            $table->dropForeign(['product_id']);
            $table->dropForeign(['customer_id']);

            // ユニークインデックスの削除
            $table->dropUnique('reviews_product_id_customer_id_unique');

            // 外部キー制約の再追加
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }
}
