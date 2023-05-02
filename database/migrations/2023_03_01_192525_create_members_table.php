<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('individual');
            $table->string('uid')->unique();
            $table->string('lastname');
            $table->string('firstname')->nullable();
            $table->string('othernames')->nullable();
            $table->string('fullname');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('gender');
            $table->integer('branchid');
            $table->string('branch');
            $table->string('wing');
            $table->string('created_by');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('members');
    }
}
