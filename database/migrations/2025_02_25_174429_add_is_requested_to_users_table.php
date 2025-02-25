<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'isRequested')) { // Check if column already exists
            $table->boolean('isRequested')->nullable()->after('status');
        }

        if (!Schema::hasColumn('users', 'request_status')) { // Ensure request_status column exists
            $table->enum('request_status', ['pending', 'denied', 'approved'])->nullable()->after('isRequested');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'isRequested')) {
            $table->dropColumn('isRequested');
        }

        if (Schema::hasColumn('users', 'request_status')) {
            $table->dropColumn('request_status');
        }
    });
}


};
