<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_leaks_table.php
class CreateLeaksTable extends Migration {
    public function up() {
        // database/migrations/xxxx_xx_xx_create_leaks_table.php
            Schema::create('leaks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('location');
                $table->text('description');
                $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
                $table->enum('status', ['pending', 'investigating', 'resolved'])->default('pending');
                $table->string('image')->nullable();
                $table->string('contact_info')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
                }

    public function down() {
        Schema::dropIfExists('leaks');
    }
}
