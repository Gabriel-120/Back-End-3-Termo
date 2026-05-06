<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Str;

class CustomPokemonRepository
{
    private const FIRST_CUSTOM_POKEDEX_NUMBER = 10001;

    public function all(): array
    {
        return Pokemon::query()
            ->orderBy('pokedex_number')
            ->get()
            ->map(fn (Pokemon $pokemon) => $this->toArray($pokemon))
            ->all();
    }

    public function findByPokedexNumber($pokedexNumber): ?array
    {
        if (! is_numeric($pokedexNumber)) {
            return null;
        }

        $pokemon = Pokemon::query()
            ->where('pokedex_number', (int) $pokedexNumber)
            ->first();

        return $pokemon ? $this->toArray($pokemon) : null;
    }

    public function nextPokedexNumber(): int
    {
        $max = Pokemon::query()->max('pokedex_number');

        return max(self::FIRST_CUSTOM_POKEDEX_NUMBER - 1, (int) $max) + 1;
    }

    public function create(array $pokemon): array
    {
        $pokemon['pokedex_number'] = (int) ($pokemon['pokedex_number'] ?? $this->nextPokedexNumber());
        $pokemon['slug'] = $pokemon['slug'] ?? Str::slug($pokemon['name'] ?? ('pokemon-'.$pokemon['pokedex_number']));

        $created = Pokemon::query()->create($this->modelPayload($pokemon));

        return $this->toArray($created);
    }

    public function updateByPokedexNumber(int $pokedexNumber, array $updates): ?array
    {
        $pokemon = Pokemon::query()
            ->where('pokedex_number', $pokedexNumber)
            ->first();

        if (! $pokemon) {
            return null;
        }

        $pokemon->fill($this->modelPayload(array_merge(
            $this->toArray($pokemon),
            $updates,
            ['pokedex_number' => $pokedexNumber]
        )));
        $pokemon->save();

        return $this->toArray($pokemon->refresh());
    }

    public function deleteByPokedexNumber(int $pokedexNumber): bool
    {
        return (bool) Pokemon::query()
            ->where('pokedex_number', $pokedexNumber)
            ->delete();
    }

    public function deleteByPokedexNumbers(array $pokedexNumbers): int
    {
        $numbers = array_values(array_filter(array_map('intval', $pokedexNumbers)));

        if (! $numbers) {
            return 0;
        }

        return Pokemon::query()
            ->whereIn('pokedex_number', $numbers)
            ->delete();
    }

    public function deleteAll(): int
    {
        return Pokemon::query()->delete();
    }

    public function updateEvolutionLineForNumbers(array $pokedexNumbers, array $evolutions): int
    {
        $numbers = array_values(array_filter(array_map('intval', $pokedexNumbers)));

        if (! $numbers) {
            return 0;
        }

        return Pokemon::query()
            ->whereIn('pokedex_number', $numbers)
            ->update(['evolutions' => $evolutions]);
    }

    public function toPokedexViewData(array $stored): array
    {
        $image = $this->imageFor($stored);
        $types = $this->normalizeTypes($stored['types'] ?? []);
        $stats = $this->normalizeStats($stored['stats'] ?? []);
        $moves = $this->normalizeMoves($stored['moves'] ?? [], $types[0] ?? 'normal');
        $evolutions = $this->normalizeEvolutions($stored);

        $pokemon = [
            'id' => (int) ($stored['pokedex_number'] ?? 0),
            'name' => $stored['name'] ?? 'Pokemon criado',
            'height' => (float) ($stored['height'] ?? 0),
            'weight' => (float) ($stored['weight'] ?? 0),
            'base_experience' => (int) ($stored['base_experience'] ?? 0),
            'sprites' => [
                'front_default' => $image,
                'other' => [
                    'official-artwork' => [
                        'front_default' => $image,
                    ],
                ],
            ],
            'types' => array_map(function ($type, $slot) {
                return [
                    'slot' => $slot + 1,
                    'type' => ['name' => $type],
                ];
            }, $types, array_keys($types)),
            'stats' => $stats,
            'moves' => [],
            'abilities' => array_map(function ($ability) {
                return [
                    'ability' => ['name' => $ability],
                    'is_hidden' => false,
                ];
            }, $stored['abilities'] ?? []),
        ];

        return [
            'pokemon' => $pokemon,
            'species' => $stored['meta'] ?? [],
            'evolutions' => $evolutions,
            'variants' => $stored['variants'] ?? [],
            'moves' => $moves,
            'description' => $stored['description'] ?? 'Descricao nao disponivel.',
            'locations' => $stored['locations'] ?? [],
            'showBackButton' => false,
            'isCustomPokemon' => true,
        ];
    }

    public function toListCard(array $stored): array
    {
        $evolutions = $this->normalizeEvolutions($stored);

        return [
            'source' => $stored['source'] ?? 'manual',
            'id' => (int) ($stored['pokedex_number'] ?? 0),
            'name' => $this->displayName($stored['name'] ?? 'Pokemon criado'),
            'image' => $this->imageFor($stored),
            'types' => $this->normalizeTypes($stored['types'] ?? []),
            'generation' => $this->generationLabel($stored['generation'] ?? 'custom'),
            'evolution_count' => max(0, count($evolutions) - 1),
            'is_custom' => true,
            'detail_url' => route('pokedex.show', ['id' => (int) ($stored['pokedex_number'] ?? 0)]),
        ];
    }

    public function toFusionSummary(array $stored): array
    {
        $stats = [];
        foreach ($this->normalizeStats($stored['stats'] ?? []) as $stat) {
            $stats[$stat['stat']['name']] = (int) $stat['base_stat'];
        }

        return [
            'ref' => 'custom:'.($stored['pokedex_number'] ?? ''),
            'id' => (int) ($stored['pokedex_number'] ?? 0),
            'name' => $this->displayName($stored['name'] ?? 'Pokemon criado'),
            'types' => $this->normalizeTypes($stored['types'] ?? []),
            'height' => (float) ($stored['height'] ?? 0),
            'weight' => (float) ($stored['weight'] ?? 0),
            'image_url' => $this->imageFor($stored),
            'description' => $stored['description'] ?? '',
            'abilities' => $stored['abilities'] ?? [],
            'moves' => array_column($this->normalizeMoves($stored['moves'] ?? []), 'name'),
            'stats' => $stats,
            'generation' => $stored['generation'] ?? 'custom',
        ];
    }

    public function placeholderImage(string $name): string
    {
        return 'https://placehold.co/320x320/0f172a/facc15?text='.rawurlencode($this->displayName($name));
    }

    private function toArray(Pokemon $pokemon): array
    {
        return [
            'internal_id' => $pokemon->id,
            'pokedex_number' => $pokemon->pokedex_number,
            'name' => $pokemon->name,
            'slug' => $pokemon->slug,
            'source' => $pokemon->source,
            'generation' => $pokemon->generation,
            'rarity' => $pokemon->rarity,
            'image_path' => $pokemon->image_path,
            'image_url' => $pokemon->image_url,
            'height' => $pokemon->height,
            'weight' => $pokemon->weight,
            'base_experience' => $pokemon->base_experience,
            'description' => $pokemon->description,
            'types' => $pokemon->types ?? [],
            'abilities' => $pokemon->abilities ?? [],
            'stats' => $pokemon->stats ?? [],
            'moves' => $pokemon->moves ?? [],
            'evolutions' => $pokemon->evolutions ?? [],
            'variants' => $pokemon->variants ?? [],
            'locations' => $pokemon->locations ?? [],
            'meta' => $pokemon->meta ?? [],
            'fusion' => $pokemon->fusion ?? [],
            'ai_payload' => $pokemon->ai_payload ?? [],
            'created_at' => optional($pokemon->created_at)->toISOString(),
            'updated_at' => optional($pokemon->updated_at)->toISOString(),
        ];
    }

    private function modelPayload(array $pokemon): array
    {
        return [
            'pokedex_number' => (int) ($pokemon['pokedex_number'] ?? $this->nextPokedexNumber()),
            'name' => $pokemon['name'] ?? 'Pokemon criado',
            'slug' => $pokemon['slug'] ?? Str::slug($pokemon['name'] ?? 'pokemon-criado'),
            'source' => $pokemon['source'] ?? 'manual',
            'generation' => $pokemon['generation'] ?? 'custom',
            'rarity' => $pokemon['rarity'] ?? data_get($pokemon, 'meta.rarity'),
            'image_path' => $pokemon['image_path'] ?? null,
            'image_url' => $pokemon['image_url'] ?? null,
            'height' => (float) ($pokemon['height'] ?? 0),
            'weight' => (float) ($pokemon['weight'] ?? 0),
            'base_experience' => (int) ($pokemon['base_experience'] ?? 0),
            'description' => $pokemon['description'] ?? null,
            'types' => array_values($pokemon['types'] ?? []),
            'abilities' => array_values($pokemon['abilities'] ?? []),
            'stats' => $pokemon['stats'] ?? [],
            'moves' => array_values($pokemon['moves'] ?? []),
            'evolutions' => array_values($pokemon['evolutions'] ?? []),
            'variants' => array_values($pokemon['variants'] ?? []),
            'locations' => array_values($pokemon['locations'] ?? []),
            'meta' => $pokemon['meta'] ?? [],
            'fusion' => $pokemon['fusion'] ?? [],
            'ai_payload' => $pokemon['ai_payload'] ?? [],
        ];
    }

    private function imageFor(array $stored): string
    {
        if (! empty($stored['image_path'])) {
            return '/storage/'.ltrim($stored['image_path'], '/');
        }

        if (! empty($stored['image_url'])) {
            return $stored['image_url'];
        }

        return $this->placeholderImage($stored['name'] ?? 'Pokemon');
    }

    private function normalizeTypes(array $types): array
    {
        return array_values(array_unique(array_filter(array_map(function ($type) {
            if (is_array($type)) {
                $type = $type['type']['name'] ?? $type['name'] ?? null;
            }

            return $type ? strtolower(trim((string) $type)) : null;
        }, $types))));
    }

    private function normalizeStats(array $stats): array
    {
        $defaults = [
            'hp' => 45,
            'attack' => 45,
            'defense' => 45,
            'special-attack' => 45,
            'special-defense' => 45,
            'speed' => 45,
        ];

        foreach ($stats as $key => $value) {
            if (is_array($value)) {
                $statName = $value['stat']['name'] ?? $value['name'] ?? $key;
                $defaults[$this->normalizeStatName($statName)] = (int) ($value['base_stat'] ?? $value['value'] ?? 45);
                continue;
            }

            $defaults[$this->normalizeStatName($key)] = (int) $value;
        }

        return array_map(function ($name, $value) {
            return [
                'base_stat' => max(1, (int) $value),
                'effort' => 0,
                'stat' => ['name' => $name],
            ];
        }, array_keys($defaults), $defaults);
    }

    private function normalizeMoves(array $moves, string $fallbackType = 'normal'): array
    {
        $normalized = [];

        foreach ($moves as $move) {
            if (is_string($move)) {
                $move = ['name' => $move];
            }

            if (! is_array($move) || empty($move['name'])) {
                continue;
            }

            $normalized[] = [
                'name' => $this->displayName($move['name']),
                'type' => $this->displayName($move['type'] ?? $fallbackType),
                'category' => $this->displayName($move['category'] ?? 'status'),
                'power' => $move['power'] ?? '-',
                'accuracy' => $move['accuracy'] ?? '-',
                'pp' => $move['pp'] ?? '-',
                'effect' => $move['effect'] ?? 'Ataque criado para este Pokemon.',
            ];
        }

        if ($normalized) {
            return $normalized;
        }

        return [[
            'name' => 'Investida Criada',
            'type' => $this->displayName($fallbackType),
            'category' => 'Fisico',
            'power' => 40,
            'accuracy' => 100,
            'pp' => 35,
            'effect' => 'Um ataque basico criado para este Pokemon personalizado.',
        ]];
    }

    private function normalizeEvolutions(array $stored): array
    {
        $evolutions = $stored['evolutions'] ?? [];
        $image = $this->imageFor($stored);

        if (! $evolutions) {
            $evolutions = [[
                'id' => (int) ($stored['pokedex_number'] ?? 0),
                'name' => $stored['name'] ?? 'Pokemon criado',
                'image' => $image,
            ]];
        }

        return array_map(function ($evolution) use ($stored, $image) {
            if (is_string($evolution)) {
                $evolution = ['name' => $evolution];
            }

            $name = $evolution['name'] ?? ($stored['name'] ?? 'Pokemon criado');
            $evolutionImage = $evolution['image'] ?? null;

            if ($evolutionImage && ! str_starts_with($evolutionImage, 'http') && ! str_starts_with($evolutionImage, '/')) {
                $evolutionImage = '/storage/'.ltrim($evolutionImage, '/');
            }

            return [
                'id' => $evolution['id'] ?? null,
                'name' => $this->displayName($name),
                'image' => $evolutionImage ?: $image,
            ];
        }, $evolutions);
    }

    private function normalizeStatName(string $name): string
    {
        return str_replace('_', '-', strtolower(trim($name)));
    }

    private function displayName(string $name): string
    {
        return Str::of($name)->replace('-', ' ')->title()->toString();
    }

    private function generationLabel(string $generation): string
    {
        return match ($generation) {
            '1' => 'Geracao 1 - Kanto',
            '2' => 'Geracao 2 - Johto',
            '3' => 'Geracao 3 - Hoenn',
            '4' => 'Geracao 4 - Sinnoh',
            '5' => 'Geracao 5 - Unova',
            '6' => 'Geracao 6 - Kalos',
            '7' => 'Geracao 7 - Alola',
            '8' => 'Geracao 8 - Galar/Hisui',
            '9' => 'Geracao 9 - Paldea',
            default => 'Criado/Fusion',
        };
    }
}
