<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_home_usage_table.php
class CreateSensorTable extends Migration {
    // database/migrations/xxxx_xx_xx_create_home_usage_table.php
public function up()
{
    Schema::create('sensor', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('area'); // kitchen, bathroom, garden
        $table->float('usage'); // distributed usage
        $table->float('flow_rate'); // raw sensor value
        $table->float('total_used'); // raw sensor value
        $table->date('date');
        $table->timestamps();
    });
}


    public function down() {
        Schema::dropIfExists('sensor');
    }
}
