<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_no')->after('email')->nullable();
            $table->string('house_no')->after('phone_no')->nullable();
            $table->string('address')->after('house_no')->nullable();
            $table->string('city')->after('address')->nullable();
            $table->string('avatar')->after('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_no');
            $table->dropColumn('house_no');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('avatar');
        });
    }
};
