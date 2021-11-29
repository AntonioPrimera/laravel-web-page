<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lwp_components', function (Blueprint $table) {
            $table->id();
	
			$table->string('type');
            $table->string('name');
            $table->string('uid');
            
			$table->json('definition')->nullable()->comment('The definitions of the data attributes for bit components');
			$table->json('data')->nullable()->comment('The actual data container for bit components.');
			
			$table->foreignId('parent_id')->nullable()->constrained('lwp_components');
            
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
        Schema::dropIfExists('lwp_components');
    }
}
