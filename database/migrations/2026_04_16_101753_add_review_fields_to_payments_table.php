<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('reference', 120)->nullable()->after('payment_method');
            $table->text('notes')->nullable()->after('reference');
            $table->unsignedBigInteger('validated_by')->nullable()->after('status');
            $table->timestamp('validated_at')->nullable()->after('validated_by');

            $table->foreign('validated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn([
                'reference',
                'notes',
                'validated_by',
                'validated_at',
            ]);
        });
    }
};
