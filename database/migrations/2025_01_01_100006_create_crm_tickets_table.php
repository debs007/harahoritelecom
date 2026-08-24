<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_tickets', function (Blueprint $t) {
            $t->id();
            $t->string('ticket_number')->unique();
            $t->foreignId('crm_contact_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $t->string('subject');
            $t->text('description');
            $t->enum('status', ['open','in_progress','waiting','resolved','closed'])->default('open');
            $t->enum('priority', ['low','medium','high','urgent'])->default('medium');
            $t->enum('category', ['order_issue','payment','product','return','other'])->default('other');
            $t->timestamp('sla_due_at')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->text('resolution')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_tickets'); }
};
