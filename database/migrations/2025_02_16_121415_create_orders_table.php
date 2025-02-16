<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('receipts_id')->nullable();
            $table->enum('payment_type', ['cash', 'online'])->default('cash');
            $table->enum('payment_status', ['pending', 'failed', 'success'])->default('pending');
            $table->string('method', 50)->nullable();
            $table->string('payment_id', 100)->nullable();
            $table->string('card_id', 50)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('vpa', 100)->nullable();
            $table->string('upi_transaction_id', 100)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
