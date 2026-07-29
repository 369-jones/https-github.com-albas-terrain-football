<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('country', 2)->nullable()->after('phone'); // ISO 3166-1 alpha-2 e.g. CI, SN, NG, CD
            $table->string('preferred_locale', 5)->default('fr')->after('country');
            $table->string('preferred_currency', 3)->default('XOF')->after('preferred_locale');
            $table->boolean('is_active')->default(true)->after('preferred_currency');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'country', 'preferred_locale', 'preferred_currency', 'is_active']);
        });
    }
};
