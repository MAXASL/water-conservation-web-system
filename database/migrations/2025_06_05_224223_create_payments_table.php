<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In the migration file
public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->decimal('amount_due', 10, 2);
        $table->decimal('amount_paid', 10, 2)->default(0);
        $table->decimal('rate_per_liter', 8, 2);
        $table->date('due_date');
        $table->date('paid_date')->nullable();
        $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
