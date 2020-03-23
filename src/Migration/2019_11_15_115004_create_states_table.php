<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->string('name');
			$table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
			$table->string('state_type', 20);
			$table->string('state_key', 20);

			$table->unique(['state_type', 'state_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
