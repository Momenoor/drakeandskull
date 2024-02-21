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
        Schema::create('case_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number');
            $table->text('request_text');
            $table->string('request_type');
            $table->dateTime('request_date');
            $table->string('request_by')->nullable();
            $table->string('decision_number')->nullable();
            $table->dateTime('decision_date')->nullable();
            $table->text('decision_text')->nullable();
            $table->string('decision_by')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_requests');
    }
};
