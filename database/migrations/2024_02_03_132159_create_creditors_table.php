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
        Schema::create('creditors', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->string('creditor_name_ar')->nullable();
            $table->string('creditor_name_en')->nullable();
            $table->string('case_number')->nullable();
            $table->string('execution_number')->nullable();
            $table->decimal('execution_amount', 10, 2)->nullable();
            $table->string('legal_representative')->nullable();
            $table->date('claim_submission_date')->nullable();
            $table->decimal('claim_amount', 10, 2)->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('email3')->nullable();
            $table->string('email4')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditors');
    }
};
