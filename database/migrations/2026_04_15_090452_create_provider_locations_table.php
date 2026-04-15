<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_locations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('assistance_request_id');

            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);

            $table->decimal('accuracy', 8, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->decimal('speed', 8, 2)->nullable();

            $table->timestamp('recorded_at')->nullable();

            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')
                ->on('providers')
                ->onDelete('cascade');

            $table->foreign('assistance_request_id')
                ->references('id')
                ->on('assistance_requests')
                ->onDelete('cascade');

            $table->index(['provider_id', 'assistance_request_id'], 'provider_locations_provider_request_index');
            $table->index(['assistance_request_id', 'recorded_at'], 'provider_locations_request_recorded_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_locations');
    }
};
