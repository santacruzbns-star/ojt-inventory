<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personnel_items', function (Blueprint $table) {
            $table->id('personnel_item_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('personnel_id');
            $table->integer('personnel_item_quantity');
            $table->date('personnel_date_receive')->nullable();
            $table->date('personnel_date_issued')->nullable();
            $table->string('personnel_item_remarks');
            $table->timestamps();


            $table->foreign('item_id')
                ->references('item_id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('personnel_id')
                ->references('personnel_id')
                ->on('personnels')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_items');
    }
};
