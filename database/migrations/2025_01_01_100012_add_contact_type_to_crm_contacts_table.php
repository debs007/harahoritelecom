<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('crm_contacts', function (Blueprint $t) {
            $t->enum('contact_type', ['buyer','registered','tally_import','manual','prospect'])
              ->default('prospect')
              ->after('source')
              ->comment('buyer=placed orders, registered=signed up no orders, tally_import=from tally, manual=added manually');
        });
    }
    public function down(): void {
        Schema::table('crm_contacts', function (Blueprint $t) {
            $t->dropColumn('contact_type');
        });
    }
};
