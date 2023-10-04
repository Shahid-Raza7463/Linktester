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
        Schema::create('links_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id');
            // $table->text('response');
            $table->json('response');
            $table->integer('status_code');
            $table->integer('network_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_report');
    }
};
