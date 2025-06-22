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
        if(!Schema::hasTable('conductors')) {
            Schema::create('conductors', function (Blueprint $table) {
                $table->id()->autoIncement()->nullable(false);
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('first_name')->nullable(false);
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable(false);
                $table->string('staff_id')->nullable(false);
                $table->string('email')->unique()->nullable();
                $table->string('phone_number')->nullable()->unique();
                $table->string('department_name')->nullable(false);
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
        if(Schema::hasTable('conductors')) {
            Schema::dropIfExists('conductors');
        }
    }
};
