<?php

namespace App\Observers;

use App\Models\item_pedido;
use App\Models\estoque;

class ItemPedidoObserver
{
    /**
     * Handle the ItemPedido "created" event.
     * Quando um item é adicionado ao pedido, desconta do estoque
     */
    public function created(item_pedido $itemPedido): void
    {
        $estoque = $itemPedido->produto->estoque;
        
        if ($estoque) {
            // Desconta a quantidade do estoque
            $estoque->quantidade -= $itemPedido->quantidade;
            $estoque->save();
        }
    }

    /**
     * Handle the ItemPedido "updated" event.
     * Quando um item é editado, recalcula o estoque
     */
    public function updated(item_pedido $itemPedido): void
    {
        // Pega os valores antigos
        $quantidadeAntiga = $itemPedido->getOriginal('quantidade');
        $quantidadeNova = $itemPedido->quantidade;
        
        // Se a quantidade foi alterada, ajusta o estoque
        if ($quantidadeAntiga !== $quantidadeNova) {
            $estoque = $itemPedido->produto->estoque;
            
            if ($estoque) {
                // Calcula a diferença e ajusta o estoque
                $diferenca = $quantidadeAntiga - $quantidadeNova;
                $estoque->quantidade += $diferenca;
                $estoque->save();
            }
        }
    }

    /**
     * Handle the ItemPedido "deleted" event.
     * Quando um item é removido do pedido, devolve ao estoque
     */
    public function deleted(item_pedido $itemPedido): void
    {
        $estoque = $itemPedido->produto->estoque;
        
        if ($estoque) {
            // Devolve a quantidade ao estoque
            $estoque->quantidade += $itemPedido->quantidade;
            $estoque->save();
        }
    }

    /**
     * Handle the ItemPedido "restored" event.
     */
    public function restored(item_pedido $itemPedido): void
    {
        //
    }

    /**
     * Handle the ItemPedido "force deleted" event.
     */
    public function forceDeleted(item_pedido $itemPedido): void
    {
        //
    }
}
