<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estoque extends Model
{
    protected $guarded = [];
    protected $casts = [
        'data_validade' => 'date',
    ];

    public function produto()
    {
        return $this->belongsTo(produto::class);
    }

    /**
     * Verifica se estoque está baixo
     */
    public function estaAbaixoDoMinimo(): bool
    {
        return $this->quantidade <= $this->quantidade_minima;
    }
}
