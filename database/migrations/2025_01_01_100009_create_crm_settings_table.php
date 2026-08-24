<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->string('label')->nullable();
            $t->string('type')->default('text'); // text, number, boolean
            $t->timestamps();
        });

        // Seed default settings
        DB::table('crm_settings')->insert([
            ['key'=>'loyalty_point_value',    'value'=>'0.25', 'label'=>'1 Point = ₹ (INR value)', 'type'=>'number', 'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'loyalty_points_per_100', 'value'=>'1',    'label'=>'Points earned per ₹100 spent', 'type'=>'number', 'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'loyalty_max_redemption', 'value'=>'10',   'label'=>'Max redemption % of order value', 'type'=>'number', 'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('crm_settings'); }
};
