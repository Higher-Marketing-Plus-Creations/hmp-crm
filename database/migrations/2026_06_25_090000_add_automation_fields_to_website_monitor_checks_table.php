<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_monitor_checks', function (Blueprint $table) {
            $table->timestamp('last_checked_at')->nullable()->after('website_status');
            $table->unsignedInteger('response_time_ms')->nullable()->after('failed_form_count');
            $table->timestamp('ssl_expiry_date')->nullable()->after('ssl_status');
            $table->integer('ssl_days_left')->nullable()->after('ssl_expiry_date');
            $table->text('last_error')->nullable()->after('check_summary');
        });
    }

    public function down(): void
    {
        Schema::table('website_monitor_checks', function (Blueprint $table) {
            $table->dropColumn([
                'last_checked_at',
                'response_time_ms',
                'ssl_expiry_date',
                'ssl_days_left',
                'last_error',
            ]);
        });
    }
};
