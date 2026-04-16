<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_vehicle_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedSmallInteger('vehicle_type_id');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('night_surcharge', 10, 2)->default(0);
            $table->decimal('weekend_surcharge', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'vehicle_type_id'], 'svc_vehicle_rate_unique');

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');

            $table->foreign('vehicle_type_id')
                ->references('id')
                ->on('vehicle_types')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_vehicle_rates');
    }
};
