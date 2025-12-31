<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->enum('finish_action', [
                'goHome',
                'land',
                'hover',
            ])->default('goHome');

            $table->enum('rc_lost_action', [
                'goHome',
                'land',
                'hover',
            ])->default('goHome');

            $table->decimal('global_speed', 5, 2)->default(6.00);

            // takeoff reference
            $table->decimal('takeoff_lat', 10, 7)->nullable();
            $table->decimal('takeoff_lng', 10, 7)->nullable();
            $table->decimal('takeoff_alt', 6, 2)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
