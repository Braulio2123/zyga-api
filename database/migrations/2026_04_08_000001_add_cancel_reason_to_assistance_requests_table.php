<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('assistance_requests', 'cancel_reason')) {
                $table->string('cancel_reason', 255)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('assistance_requests', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
        });
    }
};
