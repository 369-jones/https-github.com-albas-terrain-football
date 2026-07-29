<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2); // amount attributed from this payment, in the payment's own currency
            $table->timestamps();

            // A payment can only be locked into one *active* (non-failed) payout at a time —
            // enforced in the app layer (see PayoutController) since "active" depends on the
            // related payout's status, which a plain unique index can't express.
            $table->unique(['payout_id', 'payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_items');
    }
};
