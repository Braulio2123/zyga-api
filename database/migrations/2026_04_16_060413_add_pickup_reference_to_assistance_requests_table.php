<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->string('pickup_reference', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->dropColumn('pickup_reference');
        });
    }
};
