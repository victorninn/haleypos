<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('primary_color', 16)->default('#f97316')->after('logo_path');
            $table->boolean('is_active')->default(true)->after('settings');
            $table->timestamp('archived_at')->nullable()->after('is_active');
            $table->string('subscription_status', 20)->default('trial')->after('archived_at');
            $table->softDeletes()->after('updated_at');

            $table->index('is_active');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['subscription_status']);
            $table->dropSoftDeletes();
            $table->dropColumn(['primary_color', 'is_active', 'archived_at', 'subscription_status']);
        });
    }
};
