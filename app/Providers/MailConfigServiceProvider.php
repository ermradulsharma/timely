<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole() && isset($_SERVER['argv']) && (in_array('package:discover', $_SERVER['argv']) || in_array('route:list', $_SERVER['argv']) || in_array('config:cache', $_SERVER['argv']) || in_array('config:clear', $_SERVER['argv']) || in_array('route:clear', $_SERVER['argv']) || in_array('view:clear', $_SERVER['argv']) || in_array('cache:clear', $_SERVER['argv']))) {
            return;
        }
        try {
            if (!Schema::hasTable('settings') || !Schema::hasColumn('settings', 'name')) {
                return;
            }
            $settings = Setting::whereIn('name', ['smtp', 'stripe', 'push_notification_server_key', 'debug_mode'])->get()->keyBy('name');
        } catch (\Exception $e) {
            return;
        }

        /******************************************************* SMTP *******************************************************/
        if (isset($settings['smtp'])) {
            $smtp = $settings['smtp']->value ?? [];
            Config::set([
                'mail.mailers.smtp.host' => $smtp['host'] ?? 'smtp.gmail.com',
                'mail.mailers.smtp.port' => $smtp['port'] ?? 587,
                'mail.mailers.smtp.username' => $smtp['email'] ?? null,
                'mail.mailers.smtp.password' => $smtp['password'] ?? null,
                'mail.from.address' => $smtp['from_address'] ?? null,
                'mail.from.name' => $smtp['from_name'] ?? null,
            ]);
        }

        /******************************************************* STRIPE *******************************************************/
        if (isset($settings['stripe'])) {
            $stripe = $settings['stripe']->value ?? [];
            Config::set([
                'services.stripe.secret_key' => $stripe['secret_key'] ?? config('services.stripe.secret_key'),
                'services.stripe.public_key' => $stripe['public_key'] ?? config('services.stripe.public_key'),
            ]);
        }

        /******************************************************* PUSH NOTIFICATION *******************************************************/
        if (isset($settings['push_notification_server_key'])) {
            $pushKey = $settings['push_notification_server_key']->value ?? [];
            Config::set([
                'services.fcm.server_key' => $pushKey['push_notification_server_key'] ?? config('services.fcm.server_key'),
            ]);
        }

        /******************************************************* DEBUG MODE *******************************************************/
        if (isset($settings['debug_mode'])) {
            $debug = $settings['debug_mode']->value ?? [];
            Config::set([
                'app.debug' => $debug['debug_mode'] ?? config('app.debug'),
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
