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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->integer('amount');
            $table->string('location', 100);
            $table->string('model', 255);
            $table->string('network', 255);
            $table->tinyInteger('requested_data')->default(0)->comment('0-Inactive, 1-Active');
            $table->tinyInteger('assigned_data')->default(0)->comment('0-Inactive, 1-Active');
            $table->string('start_time', 10);
            $table->string('end_time', 10);
            $table->string('outbound_date', 10);
            $table->string('inbound_date', 10);
            $table->tinyInteger('confirmed');
            $table->integer('checkout_amount')->nullable();
            $table->integer('checkin_amount')->nullable();
            $table->timestamp('checkout_time')->nullable();
            $table->timestamp('checkin_time')->nullable();
            $table->timestamps();

            $table->index('order_id', 'rentals_order_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
