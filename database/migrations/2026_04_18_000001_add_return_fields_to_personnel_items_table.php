<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personnel_items', function (Blueprint $table) {
            $table->string('item_remark')->nullable()->after('personnel_item_remarks');
            $table->string('return_reason_preset', 64)->nullable()->after('item_remark');
            $table->text('return_reason_detail')->nullable()->after('return_reason_preset');
        });
    }

    public function down(): void
    {
        Schema::table('personnel_items', function (Blueprint $table) {
            $table->dropColumn(['item_remark', 'return_reason_preset', 'return_reason_detail']);
        });
    }
};
