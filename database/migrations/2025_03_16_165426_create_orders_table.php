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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('fly_order_id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('confirmed')->nullable();
            $table->integer('is_manual')->nullable();
            $table->integer('sibling_order_id');
            $table->timestamps();
            $table->text('pickup_time')->nullable();
            $table->text('dropoff_time')->nullable();
            $table->date('tentative_date')->nullable();
            $table->text('carrier')->nullable();
            $table->text('other_carrier')->nullable();
            $table->string('custom_line_items');
            $table->string('shipping_speed', 50);
            $table->string('shipping_type')->nullable()->comment('Expedited, Standard');
            $table->text('ship_city')->nullable();
            $table->string('site_code')->nullable();
            $table->text('ship_state')->nullable();
            $table->string('ship_country')->nullable();
            $table->text('ship_zip')->nullable();
            $table->string('customer_title')->nullable();
            $table->string('grand_total')->nullable();
            $table->text('ordered_ims_items')->nullable();
            $table->string('site_source', 100)->nullable();
            $table->string('item_total')->nullable()->comment('SubTotal from cart');
            $table->string('tax_total')->nullable()->comment('TaxTotal from cart');
            $table->boolean('is_auto_shipping')->default(true)->comment('auto-1 , manual-0');
            $table->string('shipping_total')->nullable()->comment('ShippingTotal from cart');
            $table->boolean('is_auto_discount')->default(true)->comment('1-auto, 0-custom');
            $table->string('discount_amount')->nullable()->comment('Discount amount from cart');
            $table->integer('total_gb_amount')->default(0);
            $table->text('media_installation')->nullable();
            $table->string('hardware_insurance')->nullable();
            $table->string('shipping_insurance')->nullable();
            $table->decimal('balance_amount', 8, 2)->default(0.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('outbound_date')->nullable();
            $table->date('inbound_date')->nullable();
            $table->date('prep_date')->nullable();
            $table->date('hardware_ready')->nullable();
            $table->boolean('is_auto_rush_fee')->default(true)->comment('1-auto , 0-manual');
            $table->decimal('rush_fee_amount', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
