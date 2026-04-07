<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pedido extends Model
{
    protected $guarded = [];

    public function cliente() {
        return $this->belongsTo(cliente::class);
    }

    public function itens() {
        return $this->hasMany(item_pedido::class);
    }
}
