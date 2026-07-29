<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_serial_no');
            $table->index('order_datetime');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index('delivery_boy_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('item_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('status');
            $table->index('tax_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_serial_no']);
            $table->dropIndex(['order_datetime']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_type']);
            $table->dropIndex(['delivery_boy_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['item_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['tax_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['branch_id']);
        });
    }
};
