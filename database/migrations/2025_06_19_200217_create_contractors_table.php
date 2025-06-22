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
        if(!Schema::hasTable('contractors')) {
            Schema::create('contractors', function (Blueprint $table) {
                $table->id()->autoIncrement()->nullable(false);
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('name')->nullable(false);
                $table->string('code')->unique()->nullable(false);
                $table->string('email')->unique()->nullable(false);
                $table->string('phone_number')->unique()->nullable(false);
                $table->string('company_name')->unique()->nullable(false);
                $table->decimal('balance', 10, 2)->nullable(false);
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
        if(Schema::hasTable('contractors')) {
            Schema::dropIfExists('contractors');
        }
    }
};
