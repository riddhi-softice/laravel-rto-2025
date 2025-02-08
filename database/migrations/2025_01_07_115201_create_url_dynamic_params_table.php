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
        Schema::create('url_dynamic_params', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->constrained('url_config')->onDelete('cascade');
            $table->string('param_key');
            $table->string('param_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_dynamic_params');
    }
};
