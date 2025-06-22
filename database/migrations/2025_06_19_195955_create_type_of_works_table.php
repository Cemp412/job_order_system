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
        if(!Schema::hasTable('type_of_works')) {
            Schema::create('type_of_works', function (Blueprint $table) {
                $table->id()->autoIncrement()->nullable(false);
                $table->string('name')->nullable(false);
                $table->decimal('rate', 10, 2)->nullable(false);
                $table->string('code')->unique()->nullable(false);
                $table->softDeletes(); //extra
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('type_of_works')) {
            Schema::dropIfExists('type_of_works');
        }
    }
};
