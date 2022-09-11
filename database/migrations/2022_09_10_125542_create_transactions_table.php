<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id', 13)->primary();
            $table->decimal('amount',62,2);
            $table->string('currency', 3);
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->string('description', 2048);
            $table->enum('status', ['APPROVED', 'REJECTED'])->nullable();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('posted_by');
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamp('fulfilled_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('posted_by')
                ->references('id')
                ->on('Users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_models');
    }
};
