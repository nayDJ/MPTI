<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sales_reports');
        Schema::dropIfExists('financial_reports');
    }

    public function down(): void
    {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->string('period');
            $table->integer('total_sales');
            $table->timestamps();
        });

        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('period');
            $table->decimal('income', 12, 2);
            $table->timestamps();
        });
    }
};
