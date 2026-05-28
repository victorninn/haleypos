<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('play_session_id')->constrained('play_sessions')->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->timestamp('issued_at');
            $table->json('snapshot')->nullable()->comment('Frozen child, package & session info');
            $table->timestamps();

            $table->index(['business_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
