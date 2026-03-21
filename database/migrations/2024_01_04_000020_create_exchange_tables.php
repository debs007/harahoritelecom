<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exchange offers: admin defines max exchange value for each product being sold
        Schema::create('exchange_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('max_exchange_value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('terms')->nullable();
            $table->timestamps();
        });

        // Exchange requests: submitted by customers during checkout
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // product being purchased
            $table->string('old_phone_brand');
            $table->string('old_phone_model');
            $table->string('imei', 20);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor']);
            $table->decimal('estimated_value', 10, 2)->default(0);
            $table->decimal('approved_value', 10, 2)->nullable();
            $table->enum('status', ['pending', 'verified', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Add exchange_data to carts
        Schema::table('carts', function (Blueprint $table) {
            $table->json('exchange_data')->nullable()->after('selected_color');
        });

        // Add exchange_request_id to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('exchange_request_id')->nullable()->after('coupon_id')
                  ->constrained('exchange_requests')->nullOnDelete();
            $table->decimal('exchange_discount', 10, 2)->default(0)->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['exchange_request_id']);
            $table->dropColumn(['exchange_request_id', 'exchange_discount']);
        });
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('exchange_data');
        });
        Schema::dropIfExists('exchange_requests');
        Schema::dropIfExists('exchange_offers');
    }
};
