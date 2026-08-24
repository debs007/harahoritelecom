<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_contacts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('whatsapp')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('pincode')->nullable();
            $t->enum('segment', ['budget','mid_range','upper_mid','premium','flagship','unclassified'])->default('unclassified');
            $t->enum('source', ['organic','referral','campaign','tally_import','walk_in','social','other'])->default('organic');
            $t->enum('status', ['active','inactive','prospect','churned'])->default('prospect');
            $t->text('notes')->nullable();
            $t->json('preferences')->nullable();
            $t->date('due_date')->nullable();
            $t->date('last_contacted_at')->nullable();
            $t->unsignedInteger('visit_count')->default(0);
            $t->decimal('total_spent', 12, 2)->default(0);
            $t->unsignedInteger('total_orders')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_contacts'); }
};
