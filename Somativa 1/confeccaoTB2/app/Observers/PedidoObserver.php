<?php

namespace App\Observers;

use App\Models\pedido;

class PedidoObserver
{
    /**
     * Handle the Pedido "deleted" event.
     * Quando um pedido inteiro é deletado, os items_pedidos também são deletados
     * e o observer ItemPedidoObserver vai devolver o estoque automaticamente
     */
    public function deleted(pedido $pedido): void
    {
        // A cascata de deleção vai chamar o observer de item_pedido
        // que vai devolver o estoque automaticamente
    }

    /**
     * Handle when pedido é restaurado (restore)
     */
    public function restored(pedido $pedido): void
    {
        // Quando um pedido é restaurado, os itens também são
        // e o observer vai descontar do estoque novamente
    }
}
