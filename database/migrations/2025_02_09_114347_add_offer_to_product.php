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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_offer')->default(0)->after('stock');
            $table->decimal('offer_percentage', 5, 2)->nullable()->after('is_offer');
            $table->string('offer_text')->nullable()->after('offer_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_offer');
            $table->dropColumn('offer_percentage');
            $table->dropColumn('offer_text');
        });
    }
};
