<?php

use dimonka2\flatstate\Flatstate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Flatstate::config('migration.enabled', true)) return;
        Schema::create(Flatstate::getStateTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->string('name');
			$table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
			$table->string('state_type', 32);
			$table->string('state_key', 32);

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
        if(!Flatstate::config('migration.enabled', true)) return;
        Schema::dropIfExists(Flatstate::getStateTable());
    }
}
