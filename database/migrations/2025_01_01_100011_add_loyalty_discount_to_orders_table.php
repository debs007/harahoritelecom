<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->decimal('loyalty_discount', 10, 2)->default(0)->after('exchange_discount');
            $t->unsignedInteger('loyalty_points_used')->default(0)->after('loyalty_discount');
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->dropColumn(['loyalty_discount', 'loyalty_points_used']);
        });
    }
};
