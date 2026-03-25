<?php

namespace App\Observers;

use App\Models\estoque;

class EstoqueObserver
{
    /**
     * Handle the Estoque "updated" event.
     * Quando estoque é editado diretamente, apenas valida se não fica negativo
     */
    public function updating(estoque $estoque): void
    {
        // Validação: impede que estoque fique negativo
        if ($estoque->quantidade < 0) {
            $estoque->quantidade = 0;
        }
    }

    /**
     * Handle the Estoque "updated" event.
     */
    public function updated(estoque $estoque): void
    {
        // Aqui você poderia adicionar logs ou auditoria de mudanças
    }
}
