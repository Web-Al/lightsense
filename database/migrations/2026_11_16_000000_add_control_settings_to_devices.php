<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('mode', 10)->default('automatic');       // automatic | manual
            $table->boolean('manual_on')->default(false);            // used only in manual mode
            $table->unsignedInteger('lux_threshold')->default(300);  // switch on below this brightness
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['mode', 'manual_on', 'lux_threshold']);
        });
    }
};
