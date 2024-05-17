<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('borrower_name');
            $table->decimal('amount', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term');
            $table->enum('status', ['approved', 'rejected', 'pending', 'active']);
            $table->dateTime('last_payment_date')->nullable()->default(null);
            $table->decimal('total_paid_amount', 10, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
}

