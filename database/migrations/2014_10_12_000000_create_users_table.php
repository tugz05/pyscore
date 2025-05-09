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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->string('student_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->longText('avatar')->nullable();
            $table->enum("status",["active","inactive"])->default("active");
            $table->enum("account_type",["student","instructor","admin"])->default("student");
            $table->date('email_verified_at')->nullable();
            $table->longText('password')->nullable();
            $table->rememberToken();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
