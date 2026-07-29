<?php

namespace App\Providers;

use App\Models\Borrowing;
use App\Models\Chat;
use App\Models\Item;
use App\Policies\BorrowingPolicy;
use App\Policies\ChatPolicy;
use App\Policies\ItemPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Borrowing::class, BorrowingPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Chat::class, ChatPolicy::class);
    }
}
