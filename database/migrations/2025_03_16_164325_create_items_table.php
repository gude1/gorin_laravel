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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 500)->charset('utf8mb3')->collation('utf8mb3_unicode_ci');
            $table->string('model', 100)->charset('utf8mb3')->collation('utf8mb3_unicode_ci');
            $table->string('color', 50)->charset('utf8mb3')->collation('utf8mb3_unicode_ci');
            $table->string('size', 50)->charset('utf8mb3')->collation('utf8mb3_unicode_ci');
            $table->string('network', 50)->charset('utf8mb3')->collation('utf8mb3_unicode_ci');
            $table->integer('total')->default(0);
            $table->boolean('no_barcode')->default(false);
            $table->timestamps();
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
