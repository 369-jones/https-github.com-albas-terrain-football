<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pitch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->decimal('total_price', 12, 2);
            $table->string('currency', 3);

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])
                ->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'failed'])
                ->default('unpaid');

            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent double-booking the same pitch/date/start_time at the DB level.
            $table->unique(['pitch_id', 'booking_date', 'start_time']);
            $table->index(['booking_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
