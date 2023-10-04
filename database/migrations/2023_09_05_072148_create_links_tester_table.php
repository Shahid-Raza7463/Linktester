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
        Schema::create('links_tester', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->string('country');
            $table->string('device');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->integer('network_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_tester');
    }
};
