<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->decimal('quoted_amount', 10, 2)->nullable()->after('pickup_reference');
            $table->decimal('final_amount', 10, 2)->nullable()->after('quoted_amount');
            $table->string('payment_status', 50)->default('pending')->after('final_amount');
            $table->string('payment_method', 50)->nullable()->after('payment_status');
            $table->json('pricing_breakdown')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'quoted_amount',
                'final_amount',
                'payment_status',
                'payment_method',
                'pricing_breakdown',
            ]);
        });
    }
};
