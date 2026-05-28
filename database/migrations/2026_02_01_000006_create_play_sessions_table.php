<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('play_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('start_time');
            $table->timestamp('expected_end_time')->nullable()->comment('Null for unlimited');
            $table->timestamp('end_time')->nullable();
            $table->decimal('final_price', 10, 2);
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            $table->unsignedInteger('extended_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('play_sessions');
    }
};
