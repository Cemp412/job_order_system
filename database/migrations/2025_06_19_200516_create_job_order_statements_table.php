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
        if(!Schema::hasTable('job_order_statements')) {
            Schema::create('job_order_statements', function (Blueprint $table) {
                $table->id();
                $table->string('reference_number')->unique();
                $table->unsignedBigInteger('contractor_id');
                $table->unsignedBigInteger('conductor_id');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('balance_amount', 12, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
                $table->foreign('conductor_id')->references('id')->on('conductors')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('job_order_statements')) {
            Schema::dropIfExists('job_order_statements');
        }
    }
};
