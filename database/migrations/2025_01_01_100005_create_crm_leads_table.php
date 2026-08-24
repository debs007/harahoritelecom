<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_leads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('crm_contact_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->decimal('value', 12, 2)->default(0);
            $t->enum('stage', ['new','contacted','qualified','proposal','negotiation','won','lost'])->default('new');
            $t->unsignedTinyInteger('score')->default(0)->comment('0-100');
            $t->enum('source', ['organic','referral','campaign','tally_import','walk_in','social','other'])->default('organic');
            $t->date('expected_close_date')->nullable();
            $t->text('notes')->nullable();
            $t->string('product_interest')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_leads'); }
};
