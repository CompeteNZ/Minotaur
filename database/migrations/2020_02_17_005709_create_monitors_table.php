<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('monitor_id');
            $table->string('monitor_type');
            $table->string('monitor_source');
            $table->integer('monitor_port')->nullable();
            $table->smallinteger('monitor_schedule');
            $table->smallinteger('monitor_alert');
            $table->datetime('monitor_last');
            $table->datetime('monitor_next');
            $table->smallinteger('monitor_state')->index('monitor_state')->default('-1');
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
        Schema::dropIfExists('monitors');
    }
}
