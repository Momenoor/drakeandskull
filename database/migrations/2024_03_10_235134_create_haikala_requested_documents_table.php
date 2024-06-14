<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('haikala_requested_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->text('requested_documents_ar')->nullable();
            $table->text('requested_documents_en')->nullable();
            $table->text('mails')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('haikala_requested_documents');
    }
};
