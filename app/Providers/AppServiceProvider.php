<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::after(function ($user, $ability, $result, $arguments) {
            if ($result === false) {
                throw new AuthorizationException('Bạn không có quyền');
            }
        });

        View::composer('*', function ($view) {

            if (request()->is('admin') || request()->is('admin/*')) {
                return;
            }
            $categories = Category::whereNull('parent_id')
                ->with('children')
                ->get();
                
            $view->with('categories', $categories);
        });

        Event::listen(Login::class, function ($event) {
            $userId = $event->user->id;

            // Chỉ restore từ DB vào session, KHÔNG store/ghi đè
            try {
                Cart::restore($userId);
            } catch (\Exception) {
                // DB chưa có cart → không làm gì cả
            }
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                try {
                    Cart::erase($event->user->id);
                } catch (\Exception) {}

                try {
                    Cart::store($event->user->id);
                } catch (\Exception) {}
            }
        });
    }
}