<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Every user gets a role. New users are viewers by default.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('viewer')->after('password');
        });

        // The oldest existing account becomes the administrator.
        $first = DB::table('users')->min('id');
        if ($first) {
            DB::table('users')->where('id', $first)->update(['role' => 'admin']);
        }

        // 2. Schedules are shared by everyone, so deleting a user must not delete
        //    their schedules. user_id now just records who created it.
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
