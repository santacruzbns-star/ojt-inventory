<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('oum_id'); 
            $table->string('item_name');
            $table->string('item_serialno');
            $table->integer('item_quantity');
            $table->string('item_status');
            $table->timestamps();
            
             $table->foreign('oum_id')
            ->references('oum_id')
            ->on('item_oums')
            ->onDelete('cascade');
        });

       
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
