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
        Schema::create('admins', function (Blueprint $table) {
            $table->integer('adm_id')->autoIncrement();
            $table->string('uname', 50)->index('uname');
            $table->string('password')->index('password');
            $table->string('name', 250);
            $table->string('email', 250);
            $table->smallInteger('status')->comment('1=Active, 2=Inactive')->index('status');
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
        Schema::dropIfExists('admins');
    }
};
