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
            
			$table->string('type');
            $table->string('name');
            $table->string('uid');
            $table->json('data')->nullable()->comment('The actual data container.');
            
            $table->foreignId('parent_id')->constrained('lwp_components');
            
            $table->softDeletes();
            
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
