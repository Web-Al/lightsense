<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per light/controller (one ESP32 each)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // e.g. "Ceiling light"
            $table->string('location');                   // e.g. "Living room"
            $table->string('area')->nullable();            // e.g. "2nd floor, Building A"
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('api_key_hash', 64)->unique();  // the secret key is stored scrambled
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        // One row every time a device reports its sensors
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('lux');                // room brightness
            $table->boolean('presence');                   // someone in the room?
            $table->boolean('lights_on')->default(false);
            $table->decimal('voltage', 6, 2)->nullable();  // volts
            $table->decimal('current', 8, 3)->nullable();  // amps
            $table->decimal('power', 8, 2)->nullable();    // watts
            $table->decimal('energy_kwh', 10, 3)->nullable(); // running total
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['device_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readings');
        Schema::dropIfExists('devices');
    }
};
