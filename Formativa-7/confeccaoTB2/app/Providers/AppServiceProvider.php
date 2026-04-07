<?php

namespace App\Providers;

use App\Models\item_pedido;
use App\Models\estoque;
use App\Models\pedido;
use App\Observers\ItemPedidoObserver;
use App\Observers\EstoqueObserver;
use App\Observers\PedidoObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Registra os observers para sincronizar estoque
        item_pedido::observe(ItemPedidoObserver::class);
        estoque::observe(EstoqueObserver::class);
        pedido::observe(PedidoObserver::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });
    }
}
