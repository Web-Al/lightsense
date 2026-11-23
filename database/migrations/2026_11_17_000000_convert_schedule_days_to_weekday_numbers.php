<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Old schedules used the words daily / weekdays / weekends.
// New schedules store the chosen weekdays: "1,2,3" = Mon, Tue, Wed ... 7 = Sun.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('schedules')->where('days', 'daily')->update(['days' => '1,2,3,4,5,6,7']);
        DB::table('schedules')->where('days', 'weekdays')->update(['days' => '1,2,3,4,5']);
        DB::table('schedules')->where('days', 'weekends')->update(['days' => '6,7']);
    }

    public function down(): void
    {
        DB::table('schedules')->where('days', '1,2,3,4,5,6,7')->update(['days' => 'daily']);
        DB::table('schedules')->where('days', '1,2,3,4,5')->update(['days' => 'weekdays']);
        DB::table('schedules')->where('days', '6,7')->update(['days' => 'weekends']);
    }
};
