<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('education');
            $table->integer('joining_year');
            $table->string('city');
            $table->integer('payment_tier');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->boolean('ever_benched')->default(false);
            $table->integer('experience_in_current_domain');
            $table->boolean('leave_or_not')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
