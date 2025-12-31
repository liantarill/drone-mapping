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
        Schema::create('waypoint_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waypoint_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('action_type', [
                'takePhoto',
                'startRecord',
                'stopRecord',
                'rotateAircraft',
                'gimbalPitch',
            ]);

            $table->json('params')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('waypoint_actions');
    }
};
