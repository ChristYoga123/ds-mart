<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use App\Filament\Custom\Responses\LogoutResponse as CustomLogoutResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponse::class, CustomLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
        Model::preventLazyLoading(!app()->isProduction());
        DB::prohibitDestructiveCommands(app()->isProduction());

        Blade::include('components.Navbar', 'Navbar');
        Blade::include('components.TextInput', 'TextInput');
        Blade::include('components.Button', 'Button');
    }
}
