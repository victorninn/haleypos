<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('plan_type', 32); // trial_1m, month_1, month_6, year_1, lifetime
            $table->timestamp('starts_at');
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 20)->default('active'); // active | expired | cancelled
            $table->boolean('is_trial')->default(false);
            $table->boolean('is_lifetime')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('business_id'); // hasOne
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
