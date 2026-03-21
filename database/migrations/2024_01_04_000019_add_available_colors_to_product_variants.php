<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // JSON array of color names available for this variant
            // e.g. ["Midnight Black", "Starlight White"]
            $table->json('available_colors')->nullable()->after('ram');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('available_colors');
        });
    }
};
