<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('discount', 8, 2)->default(0)->after('status');
            $table->decimal('final_amount', 8, 2)->nullable()->after('discount');
            $table->text('doctor_remarks')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['discount', 'final_amount', 'doctor_remarks']);
        });
    }
};