<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lwp_bits', function (Blueprint $table) {
            $table->id();
	
			$table->foreignId('parent_id')->nullable()->constrained('lwp_components');
			
            $table->string('class_name');
			$table->string('type');
            $table->string('name');
            $table->string('uid');
            $table->json('data')->nullable()->comment('The actual data container.');
            
            //$table->softDeletes();
            
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
        Schema::dropIfExists('lwp_bits');
    }
}
