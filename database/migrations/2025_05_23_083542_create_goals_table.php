<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_goals_table.php
class CreateGoalsTable extends Migration {
    public function up() {
        // database/migrations/xxxx_xx_xx_create_goals_table.php
            Schema::create('goals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->float('target_usage');
                $table->float('current_progress')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
                }

    public function down() {
        Schema::dropIfExists('goals');
    }
}
