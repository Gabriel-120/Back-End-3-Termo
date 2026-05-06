<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->query('id', rand(1, 10000));
        $id = is_numeric($id) ? (int) $id : rand(1, 10000);
        $id = max(1, $id);

        $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");

        if (! $response->successful()) {
            return "Erro ao buscar dados API";
        }

        $pokemon = $response->json();
        $species = [];
        $evolutions = [];
        $variants = [];
        $description = 'Descrição não disponível.';

        $speciesResponse = Http::get("https://pokeapi.co/api/v2/pokemon-species/{$id}");
        if ($speciesResponse->successful()) {
            $species = $speciesResponse->json();
            $description = $this->extractEnglishText(
                $species,
                'flavor_text_entries',
                'flavor_text',
                'language',
                'name'
            );

            $description = $this->translateText($description, 'en', 'pt');

            if (! empty($species['varieties']) && is_array($species['varieties'])) {
                $variants = $this->buildVariantDetails($species['varieties'], $pokemon['id']);
            }

            if (! empty($species['evolution_chain']['url'])) {
                $chainResponse = Http::get($species['evolution_chain']['url']);
                if ($chainResponse->successful()) {
                    $speciesChain = $this->parseEvolutionChain($chainResponse->json()['chain']);
                    $evolutions = $this->buildEvolutionDetails($speciesChain);
                }
            }
        }

        $showBackButton = false;
        $hasNormalVariant = false;
        $currentVariantForm = 'Normal';

        foreach ($variants as $variant) {
            if (($variant['form'] ?? '') === 'Normal') {
                $hasNormalVariant = true;
            }
            if (! empty($variant['is_current']) && ($variant['form'] ?? '') !== '') {
                $currentVariantForm = $variant['form'];
            }
        }

        if ($hasNormalVariant && strtolower($currentVariantForm) !== 'normal') {
            $showBackButton = true;
        }

        $moves = [];
        foreach (array_slice($pokemon['moves'], 0, 8) as $entry) {
            $moveResponse = Http::get($entry['move']['url']);
            if (! $moveResponse->successful()) {
                continue;
            }

            $moveData = $moveResponse->json();
            $effectEntry = collect($moveData['effect_entries'] ?? [])->first(function ($item) {
                return isset($item['language']['name']) && $item['language']['name'] === 'en';
            });

            $effect = $effectEntry['short_effect'] ?? $effectEntry['effect'] ?? 'Descrição não disponível.';
            $effect = str_replace(["\n", "\r", "\f"], ' ', $effect);
            $effect = $this->translateText($effect, 'en', 'pt');

            $moves[] = [
                'name' => ucfirst(str_replace('-', ' ', $moveData['name'])),
                'type' => ucfirst($moveData['type']['name'] ?? '---'),
                'category' => ucfirst($moveData['damage_class']['name'] ?? '---'),
                'power' => $moveData['power'] ?? '—',
                'accuracy' => $moveData['accuracy'] ?? '—',
                'pp' => $moveData['pp'] ?? '—',
                'effect' => $effect,
            ];
        }

        return view('pokemon', compact('pokemon', 'species', 'evolutions', 'variants', 'moves', 'description', 'showBackButton'));
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $query = strtolower(trim($query));
        $results = [];

        $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => 1025,
        ]);

        if (! $response->successful()) {
            return response()->json([]);
        }

        $allPokemon = $response->json()['results'] ?? [];

        foreach ($allPokemon as $poke) {
            if (count($results) >= 10) {
                break;
            }

            $pokeName = $poke['name'];
            $id = $this->extractIdFromUrl($poke['url']);

            if (is_numeric($query)) {
                if (strpos((string) $id, $query) === 0) {
                    $results[] = [
                        'id' => $id,
                        'name' => ucfirst($pokeName),
                    ];
                }
            } else {
                if (strpos($pokeName, $query) !== false) {
                    $results[] = [
                        'id' => $id,
                        'name' => ucfirst($pokeName),
                    ];
                }
            }
        }

        return response()->json($results);
    }

    public function jogo(Request $request)
    {
        $correctId = rand(1, 1025);
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$correctId}");

        if (! $response->successful()) {
            return "Erro ao buscar dados API";
        }

        $pokemon = $response->json();
        $correctName = ucfirst($pokemon['name']);

        // Gerar 4 opções falsas
        $options = [$correctName];
        $usedIds = [$correctId];

        while (count($options) < 5) {
            $fakeId = rand(1, 1025);
            if (in_array($fakeId, $usedIds)) {
                continue;
            }

            $fakeResponse = Http::get("https://pokeapi.co/api/v2/pokemon/{$fakeId}");
            if ($fakeResponse->successful()) {
                $fakePokemon = $fakeResponse->json();
                $fakeName = ucfirst($fakePokemon['name']);
                if (! in_array($fakeName, $options)) {
                    $options[] = $fakeName;
                    $usedIds[] = $fakeId;
                }
            }
        }

        // Embaralhar opções
        shuffle($options);

        return view('jogo', compact('pokemon', 'options', 'correctName'));
    }

    private function parseEvolutionChain(array $chain)
    {
        $species = [[
            'name' => $chain['species']['name'],
            'url' => $chain['species']['url'] ?? '',
        ]];

        foreach ($chain['evolves_to'] as $next) {
            $species = array_merge($species, $this->parseEvolutionChain($next));
        }

        return $species;
    }

    private function buildEvolutionDetails(array $speciesEntries): array
    {
        return array_map(function ($entry) {
            $name = ucfirst($entry['name']);
            $id = $this->extractIdFromUrl($entry['url']);
            $image = null;

            $pokemonResponse = Http::get("https://pokeapi.co/api/v2/pokemon/{$entry['name']}");
            if ($pokemonResponse->successful()) {
                $pokemonData = $pokemonResponse->json();
                $image = $pokemonData['sprites']['other']['official-artwork']['front_default']
                    ?? $pokemonData['sprites']['front_default']
                    ?? null;
                $id = $pokemonData['id'] ?? $id;
            }

            return [
                'id' => $id,
                'name' => $name,
                'image' => $image,
            ];
        }, $speciesEntries);
    }

    private function buildVariantDetails(array $varieties, int $currentId): array
    {
        $variants = [];

        foreach ($varieties as $variety) {
            if (! isset($variety['pokemon']['url'])) {
                continue;
            }

            $pokemonResponse = Http::get($variety['pokemon']['url']);
            if (! $pokemonResponse->successful()) {
                continue;
            }

            $pokemonData = $pokemonResponse->json();
            $variantName = $variety['pokemon']['name'] ?? '';
            $formName = '';

            if (strpos($variantName, '-') !== false) {
                $parts = explode('-', $variantName);
                $formName = ucfirst(implode(' ', $parts));
            } else {
                $formName = 'Normal';
            }

            $variants[] = [
                'id' => $pokemonData['id'] ?? null,
                'name' => ucfirst(str_replace('-', ' ', $variantName)),
                'form' => $formName,
                'image' => $pokemonData['sprites']['other']['official-artwork']['front_default']
                    ?? $pokemonData['sprites']['front_default']
                    ?? null,
                'is_current' => ($pokemonData['id'] ?? null) === $currentId,
            ];
        }

        return $variants;
    }

    private function extractIdFromUrl(string $url): ?int
    {
        if (preg_match('/\/(\d+)\/?$/', $url, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function translateText(string $text, string $from = 'en', string $to = 'pt'): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        $response = Http::get('https://translate.googleapis.com/translate_a/single', [
            'client' => 'gtx',
            'sl' => $from,
            'tl' => $to,
            'dt' => 't',
            'q' => $text,
        ]);

        if (! $response->successful()) {
            return $text;
        }

        $data = $response->json();
        if (! is_array($data) || empty($data[0]) || ! is_array($data[0])) {
            return $text;
        }

        $translated = '';
        foreach ($data[0] as $segment) {
            if (isset($segment[0])) {
                $translated .= $segment[0];
            }
        }

        return trim($translated) ?: $text;
    }

    private function extractEnglishText(array $data, string $entriesKey, string $textKey, string $langKey, string $langNameKey): string
    {
        if (empty($data[$entriesKey]) || ! is_array($data[$entriesKey])) {
            return 'Descrição não disponível.';
        }

        foreach ($data[$entriesKey] as $entry) {
            if (! empty($entry[$langKey][$langNameKey] ?? null) && $entry[$langKey][$langNameKey] === 'en') {
                return trim(str_replace(["\n", "\r", "\f"], ' ', $entry[$textKey]));
            }
        }

        return 'Descrição não disponível.';
    }
}
