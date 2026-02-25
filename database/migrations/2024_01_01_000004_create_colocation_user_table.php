<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colocation_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('colocation_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'member']);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colocation_user');
    }
};
