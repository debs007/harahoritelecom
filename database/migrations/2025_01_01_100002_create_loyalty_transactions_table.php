<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loyalty_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('type', ['earned','redeemed','adjusted','expired','bonus']);
            $t->integer('points');
            $t->unsignedInteger('balance_after');
            $t->string('description')->nullable();
            $t->string('reference')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('loyalty_transactions'); }
};
