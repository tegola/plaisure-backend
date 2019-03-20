<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;
use Blade;
use Carbon;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Support utf8mb4 in MySQL <5.7.7
        // https://laravel.com/docs/master/migrations#creating-indexes
        Schema::defaultStringLength(191);

        // Set locale for dates
        Carbon::setLocale(app()->getLocale());
        setlocale(LC_TIME, app()->getLocale());

        // Blade currency directive
        Blade::directive('currency', function ($value, $decimals = 2) {
        	return "<?php echo '&euro; ' . number_format($value, $decimals, ',', '.'); ?>";
        });

        // Cashier currency
        Cashier::useCurrency('eur', '€');
    }
}
