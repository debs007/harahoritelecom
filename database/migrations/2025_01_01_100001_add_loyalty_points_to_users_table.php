<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->unsignedInteger('loyalty_points')->default(0)->after('is_active');
            $t->string('city')->nullable()->after('loyalty_points');
            $t->string('state')->nullable()->after('city');
            $t->string('pincode')->nullable()->after('state');
            $t->enum('crm_segment', ['budget','mid_range','upper_mid','premium','flagship','unclassified'])->default('unclassified')->after('pincode');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['loyalty_points','city','state','pincode','crm_segment']);
        });
    }
};
