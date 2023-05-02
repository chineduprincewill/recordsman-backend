<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->string('uid');
            $table->string('fullname');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('gender');
            $table->string('member_group');
            $table->integer('groupid');
            $table->string('group');
            $table->string('event');
            $table->integer('event_year');
            $table->float('donation');
            $table->float('redeemed')->default(0.00);
            $table->string('recorder');
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
        Schema::dropIfExists('donations');
    }
}
