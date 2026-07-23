<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_leads', function (Blueprint $table) {
            $table->boolean('is_qualified')->default(false)->after('custom_data');
            $table->timestampTz('qualified_at')->nullable()->after('is_qualified');
            $table->boolean('notification_sent')->default(false)->after('qualified_at');
            $table->timestampTz('notification_sent_at')->nullable()->after('notification_sent');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_leads', function (Blueprint $table) {
            $table->dropColumn([
                'is_qualified',
                'qualified_at',
                'notification_sent',
                'notification_sent_at',
            ]);
        });
    }
};
