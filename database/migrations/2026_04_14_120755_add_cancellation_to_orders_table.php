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
        Schema::table('orders', function (Blueprint $table) {
    $table->string('cancellation_reason')->nullable()->after('status');
    $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
    $table->string('refund_status')->nullable()->after('cancelled_at');
    //$table->string('refund_reason')->nullable()->after('refund_status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
