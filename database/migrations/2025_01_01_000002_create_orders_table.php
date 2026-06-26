<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_reference')->unique(); // correlaciona pago <-> compra
            $table->string('status')->default('pending'); // sólo se guarda cuando es 'approved'
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('PEN');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('payment_id')->nullable()->index(); // id del pago en Mercado Pago
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
