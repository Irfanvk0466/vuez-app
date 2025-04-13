<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('layouts.topbar', function ($view) {
            if (Auth::check()) {
                $notifications = Notification::where('user_id', Auth::id())
                    ->where('read', false)->latest()->take(5)->get();
                $view->with('notifications', $notifications);
            }
        });
    }
}
