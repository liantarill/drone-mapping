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
        Schema::create('waypoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sequence');

            // koordinat (WGS84)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('altitude', 6, 2);

            // speed per waypoint
            $table->decimal('speed', 5, 2)->nullable();

            // heading
            $table->enum('heading_mode', [
                'auto',
                'fixed',
                'towardNextWaypoint',
            ])->default('auto');

            $table->integer('heading')->nullable(); // dipakai jika fixed

            // gimbal
            $table->integer('gimbal_pitch')->nullable(); // -90 sampai 30

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waypoints');
    }
};
