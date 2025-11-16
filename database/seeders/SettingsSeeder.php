<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Application Settings
            ['key' => 'app.name', 'value' => 'FleetManager Pro', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.currency', 'value' => 'DH', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.date_format', 'value' => 'd/m/Y', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.datetime_format', 'value' => 'd/m/Y H:i', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.timezone', 'value' => 'Africa/Casablanca', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.locale', 'value' => 'fr', 'type' => 'string', 'group' => 'app'],
            ['key' => 'app.available_locales', 'value' => '["fr","ar","en"]', 'type' => 'json', 'group' => 'app'],

            // Fleet Settings
            ['key' => 'fleet.maintenance_alert_days', 'value' => '30', 'type' => 'integer', 'group' => 'fleet'],
            ['key' => 'fleet.insurance_alert_days', 'value' => '30', 'type' => 'integer', 'group' => 'fleet'],
            ['key' => 'fleet.document_alert_days', 'value' => '30', 'type' => 'integer', 'group' => 'fleet'],
            ['key' => 'fleet.default_fuel_type', 'value' => 'diesel', 'type' => 'string', 'group' => 'fleet'],
            ['key' => 'fleet.low_stock_threshold', 'value' => '5', 'type' => 'integer', 'group' => 'fleet'],

            // Notification Settings
            ['key' => 'notifications.email_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notifications.sms_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notifications.whatsapp_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notifications.push_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notifications.admin_email', 'value' => 'admin@fleetmanager.com', 'type' => 'string', 'group' => 'notifications'],

            // Rental Settings
            ['key' => 'rental.default_daily_rate', 'value' => '300', 'type' => 'integer', 'group' => 'rental'],
            ['key' => 'rental.deposit_percentage', 'value' => '20', 'type' => 'integer', 'group' => 'rental'],
            ['key' => 'rental.late_fee_daily', 'value' => '50', 'type' => 'integer', 'group' => 'rental'],

            // Fuel Settings
            ['key' => 'fuel.alert_threshold_percentage', 'value' => '15', 'type' => 'integer', 'group' => 'fuel'],
            ['key' => 'fuel.diesel_price', 'value' => '14.50', 'type' => 'string', 'group' => 'fuel'],
            ['key' => 'fuel.gasoline_price', 'value' => '13.50', 'type' => 'string', 'group' => 'fuel'],

            // GPS/Telematics Settings (Future)
            ['key' => 'gps.enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'gps'],
            ['key' => 'gps.provider', 'value' => '', 'type' => 'string', 'group' => 'gps'],
            ['key' => 'gps.api_key', 'value' => '', 'type' => 'string', 'group' => 'gps'],

            // Mobile Money Settings (Future)
            ['key' => 'payment.mobile_money_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'payment.mpesa_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'payment.orange_money_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment'],

            // Reporting Settings
            ['key' => 'reports.default_export_format', 'value' => 'pdf', 'type' => 'string', 'group' => 'reports'],
            ['key' => 'reports.auto_backup', 'value' => '1', 'type' => 'boolean', 'group' => 'reports'],
            ['key' => 'reports.backup_frequency', 'value' => 'daily', 'type' => 'string', 'group' => 'reports'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
