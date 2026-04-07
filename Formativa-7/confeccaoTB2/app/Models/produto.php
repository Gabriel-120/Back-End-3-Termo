<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produto extends Model
{
    protected $guarded = [];

    public function estoque()
    {
        return $this->hasOne(estoque::class);
    }

    /**
     * Garante que sempre tem estoque criado
     */
    protected static function booted()
    {
        parent::booted();
        static::created(function ($produto) {
            estoque::create(['produto_id' => $produto->id]);
        });
    }
}
