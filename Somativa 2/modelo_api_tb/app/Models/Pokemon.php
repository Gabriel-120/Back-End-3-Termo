<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons';

    protected $fillable = [
        'pokedex_number',
        'name',
        'slug',
        'source',
        'generation',
        'rarity',
        'image_path',
        'image_url',
        'height',
        'weight',
        'base_experience',
        'description',
        'types',
        'abilities',
        'stats',
        'moves',
        'evolutions',
        'variants',
        'locations',
        'meta',
        'fusion',
        'ai_payload',
    ];

    protected function casts(): array
    {
        return [
            'pokedex_number' => 'integer',
            'height' => 'float',
            'weight' => 'float',
            'base_experience' => 'integer',
            'types' => 'array',
            'abilities' => 'array',
            'stats' => 'array',
            'moves' => 'array',
            'evolutions' => 'array',
            'variants' => 'array',
            'locations' => 'array',
            'meta' => 'array',
            'fusion' => 'array',
            'ai_payload' => 'array',
        ];
    }
}
