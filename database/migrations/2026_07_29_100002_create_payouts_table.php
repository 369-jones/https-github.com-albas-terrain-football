<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);

            $table->string('method'); // bank_transfer | mobile_money
            $table->json('destination'); // snapshot of the owner's payout details at request time

            $table->enum('status', ['pending', 'processing', 'paid', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
