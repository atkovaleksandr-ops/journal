<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        DB::table('attendances')
            ->whereNotIn('status', ['present', 'absent'])
            ->update(['status' => 'present']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('present','absent') NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('present','absent') NULL");
        }
    }
};
