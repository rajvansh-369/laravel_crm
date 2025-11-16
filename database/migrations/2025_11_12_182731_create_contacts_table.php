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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();                       
            $table->string('email')->nullable()->index();          
            $table->string('phone')->nullable()->index();         
            $table->enum('gender', ['male','female','other'])->nullable()->index(); 
            $table->string('profile_image_path')->nullable();      
            $table->string('additional_file_path')->nullable(); 

            $table->enum('status', ['active','merged','inactive'])->default('active')->index();
            $table->foreignId('merged_into_id')->nullable()->constrained('contacts')->nullOnDelete()->index();
            $table->timestamp('merged_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
