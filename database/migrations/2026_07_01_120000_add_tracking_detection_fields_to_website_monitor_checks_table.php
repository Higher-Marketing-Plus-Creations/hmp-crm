<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_monitor_checks', function (Blueprint $table) {
            $table->boolean('google_analytics_detected')->default(false)->after('email_delivery_status');
            $table->boolean('google_tag_manager_detected')->default(false)->after('google_analytics_detected');
            $table->boolean('google_search_console_detected')->default(false)->after('google_tag_manager_detected');
            $table->boolean('microsoft_tracking_detected')->default(false)->after('google_search_console_detected');
            $table->json('tracking_detection_details')->nullable()->after('issues');
        });
    }

    public function down(): void
    {
        Schema::table('website_monitor_checks', function (Blueprint $table) {
            $table->dropColumn([
                'google_analytics_detected',
                'google_tag_manager_detected',
                'google_search_console_detected',
                'microsoft_tracking_detected',
                'tracking_detection_details',
            ]);
        });
    }
};
