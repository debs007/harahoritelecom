<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('crm_campaigns', function (Blueprint $t) {
            $t->string('aisensy_campaign')->nullable()->after('type')
              ->comment('AiSensy campaign name — must match approved template name exactly');
        });
    }
    public function down(): void {
        Schema::table('crm_campaigns', function (Blueprint $t) {
            $t->dropColumn('aisensy_campaign');
        });
    }
};
