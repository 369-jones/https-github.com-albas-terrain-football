<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('provider', ['paystack', 'flutterwave']);
            $table->string('reference')->unique(); // our internal reference, sent to the gateway
            $table->string('provider_reference')->nullable(); // gateway's transaction id

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');

            $table->json('raw_response')->nullable(); // full gateway payload, for auditing/support
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
