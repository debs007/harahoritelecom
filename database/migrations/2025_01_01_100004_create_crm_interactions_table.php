<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_interactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('crm_contact_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['visit','call','whatsapp','sms','email','note','purchase','support']);
            $t->text('description')->nullable();
            $t->string('outcome')->nullable();
            $t->timestamp('interacted_at')->useCurrent();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_interactions'); }
};
