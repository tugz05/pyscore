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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('classlist_id', 15);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('instruction');
            $table->double('points');
            $table->date('due_date');
            $table->time('due_time');
            $table->date('accessible_date')->nullable();
            $table->time('accessible_time')->nullable();
            $table->timestamps();

            $table->foreign('classlist_id')
            ->references('id')
            ->on('classlists')
            ->onDelete('cascade'); // Optional: delete submissions if classlist is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
