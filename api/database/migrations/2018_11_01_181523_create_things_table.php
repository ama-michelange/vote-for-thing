<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThingsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('things', function (Blueprint $table) {
         $table->increments('id');
         $table->unsignedInteger('category_id');
         $table->string('title');
         $table->string('lib_title');
         $table->string('proper_title')->nullable();
         $table->string('number', 20)->nullable();
         $table->string('image_url')->nullable();
         $table->string('description_url')->nullable();
         $table->date('legal')->nullable();
         $table->text('description')->nullable();
         $table->timestamps();

         $table->index(['lib_title', 'number']);
         $table->index(['category_id', 'lib_title', 'number']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('things');
   }
}
