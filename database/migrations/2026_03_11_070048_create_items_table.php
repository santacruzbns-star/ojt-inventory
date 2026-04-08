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
            $table->unsignedBigInteger('item_category_id');
            $table->unsignedBigInteger('item_brand_id')->nullable();
            $table->unsignedBigInteger('item_uom_id');
            $table->string('item_name');
            $table->string('item_serialno')->nullable();
            $table->integer('item_quantity')->nullable();
            $table->integer('item_quantity_remaining');
            $table->string('item_quantity_status');
            $table->string('item_remark');
            $table->timestamps();

            $table->foreign('item_category_id') 
                ->references('item_category_id')
                ->on('item_categories')
                ->onDelete('cascade');

            $table->foreign('item_brand_id') 
                ->references('item_brand_id')
                ->on('item_brands')
                ->onDelete('cascade')->nullable();
            
            $table->foreign('item_uom_id') 
                ->references('item_uom_id')
                ->on('item_uoms')
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
