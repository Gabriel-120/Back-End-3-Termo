<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class item_pedido extends Model
{
    protected $guarded = [];

    public function produto() {
        return $this->belongsTo(produto::class);
    }
}
