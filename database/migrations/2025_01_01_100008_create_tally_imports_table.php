<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tally_imports', function (Blueprint $t) {
            $t->id();
            $t->string('filename');
            $t->string('original_name');
            $t->enum('status', ['pending','processing','completed','failed'])->default('pending');
            $t->unsignedInteger('total_rows')->default(0);
            $t->unsignedInteger('imported_rows')->default(0);
            $t->unsignedInteger('skipped_rows')->default(0);
            $t->text('error_log')->nullable();
            $t->json('column_map')->nullable();
            $t->timestamp('processed_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tally_imports'); }
};
