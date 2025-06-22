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
        if(!Schema::hasTable('job_orders')) {
            Schema::create('job_orders', function (Blueprint $table) {
                $table->id()->autoIncrement()->nullable(false);
                $table->string('name')->nullable(false);
                $table->date('date')->nullable(false);
                $table->date('jos_date')->nullable(false);
                $table->foreignId('type_of_work_id')->constrained('type_of_works')->onDelete('cascade');
                $table->foreignId('contractor_id')->constrained('contractors')->onDelete('cascade');
                $table->foreignId('conductor_id')->constrained('conductors')->onDelete('cascade');
                $table->decimal('actual_work_completed', 10, 2)->nullable(false);
                $table->text('remarks')->nullable(false);
                $table->string('reference_number')->unique()->nullable(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('job_orders')) {
            Schema::dropIfExists('job_orders');
        }
    }
};
