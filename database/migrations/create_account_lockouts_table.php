<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lara_care_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('authenticatable_type');
            $table->unsignedBigInteger('authenticatable_id');
            $table->string('ip_address')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlocks_at')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lara_care_lockouts');
    }
};