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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('usr_id')->autoIncrement();
            $table->string('usr_pword')->index('usr_pword')->nullable();
            $table->string('lname', 50)->index('lname');
            $table->string('fname', 50)->index('fname');
            $table->string('mname', 50)->nullable();
            $table->integer('contact_number')->nullable();
            $table->string('email')->index('email');
            $table->string('company_name')->nullable();
            $table->string('company_contact_number')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_address')->nullable();
            $table->smallInteger('status')->comment('0 = for Verification, 1 = Active, 2 = Suspended, 3 = Deactivated')->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
