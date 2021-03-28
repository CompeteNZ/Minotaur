<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_results', function (Blueprint $table) {
            $table->id();
            $table->integer('slave_id')->default(0);
            $table->string('monitor_id');
            $table->string('monitor_type');
            $table->string('monitor_source');
            $table->integer('monitor_port')->nullable();
            $table->integer('monitor_result');
            $table->string('monitor_error')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes

            $table->index(['monitor_id', 'monitor_type']);
            $table->index('monitor_result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_results');
    }
}
