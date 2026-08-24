<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_campaigns', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->enum('type', ['sms','whatsapp','email'])->default('whatsapp');
            $t->enum('status', ['draft','scheduled','running','completed','paused'])->default('draft');
            $t->text('message_template');
            $t->json('target_segments')->nullable();
            $t->timestamp('scheduled_at')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->unsignedInteger('total_recipients')->default(0);
            $t->unsignedInteger('sent_count')->default(0);
            $t->unsignedInteger('delivered_count')->default(0);
            $t->unsignedInteger('conversion_count')->default(0);
            $t->timestamps();
        });
        Schema::create('crm_campaign_contacts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('crm_campaign_id')->constrained()->cascadeOnDelete();
            $t->foreignId('crm_contact_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['pending','sent','delivered','failed','converted'])->default('pending');
            $t->timestamp('sent_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('crm_campaign_contacts');
        Schema::dropIfExists('crm_campaigns');
    }
};
