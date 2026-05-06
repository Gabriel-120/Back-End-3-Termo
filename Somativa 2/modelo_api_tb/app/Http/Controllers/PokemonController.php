<?php

namespace App\Http\Controllers;

use App\Services\CustomPokemonRepository;
use App\Services\SamplePokemonSeeder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PokemonController extends Controller
{
    private const POKEAPI_LIMIT = 1025;

    private const TYPES = [
        'normal',
        'fire',
        'water',
        'electric',
        'grass',
        'ice',
        'fighting',
        'poison',
        'ground',
        'flying',
        'psychic',
        'bug',
        'rock',
        'ghost',
        'dragon',
        'dark',
        'steel',
        'fairy',
    ];

    private const GENERATIONS = [
        '1' => ['label' => 'Geracao 1 - Kanto', 'from' => 1, 'to' => 151],
        '2' => ['label' => 'Geracao 2 - Johto', 'from' => 152, 'to' => 251],
        '3' => ['label' => 'Geracao 3 - Hoenn', 'from' => 252, 'to' => 386],
        '4' => ['label' => 'Geracao 4 - Sinnoh', 'from' => 387, 'to' => 493],
        '5' => ['label' => 'Geracao 5 - Unova', 'from' => 494, 'to' => 649],
        '6' => ['label' => 'Geracao 6 - Kalos', 'from' => 650, 'to' => 721],
        '7' => ['label' => 'Geracao 7 - Alola', 'from' => 722, 'to' => 809],
        '8' => ['label' => 'Geracao 8 - Galar/Hisui', 'from' => 810, 'to' => 905],
        '9' => ['label' => 'Geracao 9 - Paldea', 'from' => 906, 'to' => 1025],
        'custom' => ['label' => 'Criados e fusions', 'from' => 10001, 'to' => null],
    ];

    private const STAT_FIELDS = [
        'hp' => 'HP',
        'attack' => 'Ataque',
        'defense' => 'Defesa',
        'special_attack' => 'Ataque Especial',
        'special_defense' => 'Defesa Especial',
        'speed' => 'Velocidade',
    ];

    private const RARITIES = [
        'common' => 'Comum',
        'uncommon' => 'Incomum',
        'rare' => 'Raro',
        'epic' => 'Epico',
        'legendary' => 'Lendario',
        'mythical' => 'Mitico',
    ];

    public function lista(Request $request, CustomPokemonRepository $customPokemon)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => strtolower((string) $request->query('type', '')),
            'generation' => (string) $request->query('generation', ''),
            'origin' => (string) $request->query('origin', 'all'),
            'sort' => (string) $request->query('sort', 'number_asc'),
            'per_page' => min(48, max(12, (int) $request->query('per_page', 24))),
        ];

        $refs = [];

        if (! in_array($filters['origin'], ['custom', 'manual', 'fusion', 'ai'], true)) {
            $refs = array_merge($refs, $this->filterApiRefs($filters));
        }

        if ($filters['origin'] !== 'api') {
            $refs = array_merge($refs, $this->filterCustomRefs($filters, $customPokemon->all()));
        }

        $this->sortRefs($refs, $filters['sort']);

        $page = max(1, (int) $request->query('page', 1));
        $total = count($refs);
        $pageRefs = array_slice($refs, ($page - 1) * $filters['per_page'], $filters['per_page']);
        $cards = array_map(fn ($ref) => $this->buildListCard($ref, $customPokemon), $pageRefs);

        $pokemons = new LengthAwarePaginator(
            $cards,
            $total,
            $filters['per_page'],
            $page,
            [
                'path' => route('pokemon.list'),
                'query' => $request->except('page'),
            ]
        );

        return view('pokemon-list', [
            'pokemons' => $pokemons,
            'types' => self::TYPES,
            'generations' => self::GENERATIONS,
            'filters' => $filters,
        ]);
    }

    public function index(Request $request, CustomPokemonRepository $customPokemon)
    {
        $requestedId = $request->query('id');

        if ($requestedId !== null) {
            $storedPokemon = $customPokemon->findByPokedexNumber($requestedId);
            if ($storedPokemon) {
                return view('pokemon', $customPokemon->toPokedexViewData($storedPokemon));
            }
        }

        $lookup = $requestedId ?: rand(1, self::POKEAPI_LIMIT);
        $lookup = is_numeric($lookup) ? max(1, (int) $lookup) : strtolower(trim((string) $lookup));

        $pokemon = $this->getApiPokemon($lookup);

        if (! $pokemon) {
            return "Erro ao buscar dados API";
        }

        $species = [];
        $evolutions = [];
        $variants = [];
        $locations = [];
        $description = 'Descricao nao disponivel.';

        if (! empty($pokemon['species']['url'])) {
            $species = $this->pokeApiGet(
                $pokemon['species']['url'],
                [],
                'pokemon-species-'.$this->extractIdFromUrl($pokemon['species']['url']),
                72
            ) ?? [];
        } elseif (! empty($pokemon['id'])) {
            $species = $this->getApiSpecies((int) $pokemon['id']) ?? [];
        }

        if ($species) {
            $description = $this->extractEnglishText(
                $species,
                'flavor_text_entries',
                'flavor_text',
                'language',
                'name'
            );

            $description = $this->translateText($description, 'en', 'pt');

            if (! empty($species['varieties']) && is_array($species['varieties'])) {
                $variants = $this->buildVariantDetails($species['varieties'], (int) ($pokemon['id'] ?? 0));
            }

            if (! empty($species['evolution_chain']['url'])) {
                $chain = $this->getEvolutionChain($species['evolution_chain']['url']);
                if ($chain) {
                    $speciesChain = $this->parseEvolutionChain($chain['chain']);
                    $evolutions = $this->buildEvolutionDetails($speciesChain);
                }
            }
        }

        if (! empty($pokemon['id'])) {
            $encounters = $this->pokeApiGet(
                "pokemon/{$pokemon['id']}/encounters",
                [],
                'pokemon-encounters-'.$pokemon['id'],
                72
            ) ?? [];

            $uniqueLocations = [];
            foreach ($encounters as $encounter) {
                if (! empty($encounter['location_area']['name'])) {
                    $locationName = ucfirst(str_replace('-', ' ', $encounter['location_area']['name']));
                    if (! in_array($locationName, $uniqueLocations, true)) {
                        $uniqueLocations[] = $locationName;
                    }
                }
            }
            $locations = $uniqueLocations;
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

        $moves = $this->buildMoveDetails($pokemon['moves'] ?? []);
        $isCustomPokemon = false;

        return view('pokemon', compact(
            'pokemon',
            'species',
            'evolutions',
            'variants',
            'moves',
            'description',
            'locations',
            'showBackButton',
            'isCustomPokemon'
        ));
    }

    public function createChoice()
    {
        return view('pokemon-create-choice');
    }

    public function createForm(CustomPokemonRepository $customPokemon)
    {
        return view('pokemon-create', [
            'types' => self::TYPES,
            'generations' => self::GENERATIONS,
            'rarities' => self::RARITIES,
            'statFields' => self::STAT_FIELDS,
            'nextPokedexNumber' => $customPokemon->nextPokedexNumber(),
            'evolutionOptions' => $this->pokemonSelectionOptions($customPokemon),
            'groqConfigured' => filled(config('services.groq.key')),
        ]);
    }

    public function storeManual(Request $request, CustomPokemonRepository $customPokemon)
    {
        $useAi = $request->boolean('use_ai');
        $validated = $request->validate($this->manualPokemonRules($useAi));
        $pokedexNumber = (int) ($validated['pokedex_number'] ?: $customPokemon->nextPokedexNumber());

        if ($pokedexNumber <= self::POKEAPI_LIMIT) {
            return back()
                ->withErrors(['pokedex_number' => 'Use um numero acima de '.self::POKEAPI_LIMIT.' para nao conflitar com a PokeAPI.'])
                ->withInput();
        }

        if ($customPokemon->findByPokedexNumber($pokedexNumber)) {
            return back()
                ->withErrors(['pokedex_number' => 'Esse numero da Pokedex ja esta sendo usado por outro Pokemon criado.'])
                ->withInput();
        }

        $imagePath = $this->storePokemonImage($request);
        $imageUrl = $validated['image_url'] ?? null;
        $imageForRows = $imagePath ? $this->publicImageUrl($imagePath) : $imageUrl;

        $extra = $this->decodeExtraJson($validated['extra_json'] ?? null);
        if ($extra === false) {
            return back()
                ->withErrors(['extra_json' => 'O JSON extra nao esta valido.'])
                ->withInput();
        }

        if ($useAi) {
            $aiData = [];
            $aiUsed = false;

            if (filled(config('services.groq.key'))) {
                $aiData = $this->generateCreationWithGroq(
                    $validated['name'],
                    array_values($validated['types']),
                    $validated['rarity']
                );
                $aiUsed = ! empty($aiData);
            }

            $payload = $this->buildAiCreatedPokemonPayload(
                $validated,
                $pokedexNumber,
                $imagePath,
                $imageUrl,
                $aiData,
                $aiUsed
            );
        } else {
            $payload = [
            'pokedex_number' => $pokedexNumber,
            'name' => $validated['name'],
            'source' => 'manual',
            'generation' => $validated['generation'],
            'rarity' => $validated['rarity'] ?? null,
            'types' => array_values($validated['types']),
            'image_path' => $imagePath,
            'image_url' => $imageUrl,
            'height' => (float) $validated['height'],
            'weight' => (float) $validated['weight'],
            'base_experience' => (int) ($validated['base_experience'] ?? 0),
            'description' => $validated['description'],
            'abilities' => $this->linesFromText($validated['abilities'] ?? ''),
            'stats' => $this->normalizeInputStats($validated['stats'] ?? []),
            'moves' => $this->normalizeInputMoves($validated['moves'] ?? [], $validated['types'][0]),
            'evolutions' => $this->buildEvolutionRows(
                $validated['evolution_line'] ?? '',
                $validated['name'],
                $pokedexNumber,
                $imageForRows
            ),
            'variants' => $this->buildVariantRows($validated['variants'] ?? '', $imageForRows),
            'locations' => $this->linesFromText($validated['locations'] ?? ''),
            'meta' => [
                'rarity' => $validated['rarity'] ?? null,
                'color' => $validated['color'] ?? null,
                'habitat' => $validated['habitat'] ?? null,
                'shape' => $validated['shape'] ?? null,
                'growth_rate' => $validated['growth_rate'] ?? null,
                'capture_rate' => isset($validated['capture_rate']) ? (int) $validated['capture_rate'] : null,
                'is_legendary' => $request->boolean('is_legendary'),
                'is_mythical' => $request->boolean('is_mythical'),
                'is_baby' => $request->boolean('is_baby'),
                'extra' => $extra ?: [],
            ],
            ];
        }

        if (filled($validated['existing_evolution_ref'] ?? null)) {
            $imageForExistingLine = ! empty($payload['image_path'])
                ? $this->publicImageUrl($payload['image_path'])
                : ($payload['image_url'] ?? $imageForRows);

            $payload['evolutions'] = $this->buildEvolutionRowsFromExisting(
                $validated['existing_evolution_ref'],
                $validated['name'],
                $pokedexNumber,
                $imageForExistingLine,
                $customPokemon
            );
        }

        $created = $customPokemon->create($payload);

        if (filled($validated['existing_evolution_ref'] ?? null)) {
            $this->syncCustomEvolutionLine($payload['evolutions'] ?? [], $customPokemon);
        }

        return redirect()
            ->route('pokedex.show', ['id' => $created['pokedex_number']])
            ->with('success', 'Pokemon criado e adicionado na Pokedex.');
    }

    public function fusionForm(CustomPokemonRepository $customPokemon)
    {
        return view('pokemon-fusion', [
            'pokemonOptions' => $this->pokemonSelectionOptions($customPokemon),
            'groqConfigured' => filled(config('services.groq.key')),
        ]);
    }

    public function deleteIndex(CustomPokemonRepository $customPokemon)
    {
        $pokemons = array_map(function (array $pokemon) use ($customPokemon) {
            $card = $customPokemon->toListCard($pokemon);

            return array_merge($card, [
                'pokedex_number' => (int) ($pokemon['pokedex_number'] ?? 0),
                'raw_source' => $pokemon['source'] ?? 'manual',
                'rarity' => $pokemon['rarity'] ?? null,
            ]);
        }, $customPokemon->all());

        return view('pokemon-delete', [
            'pokemons' => $pokemons,
        ]);
    }

    public function editCustomPokemon(int $id, CustomPokemonRepository $customPokemon)
    {
        $pokemon = $customPokemon->findByPokedexNumber($id);

        if (! $pokemon) {
            return redirect()
                ->route('pokemon.delete.index')
                ->with('status', 'Pokemon criado/fundido nao encontrado.');
        }

        return view('pokemon-edit', [
            'pokemon' => $pokemon,
            'types' => self::TYPES,
            'generations' => self::GENERATIONS,
            'rarities' => self::RARITIES,
            'statFields' => self::STAT_FIELDS,
            'evolutionOptions' => $this->pokemonSelectionOptions($customPokemon),
        ]);
    }

    public function updateCustomPokemon(Request $request, int $id, CustomPokemonRepository $customPokemon)
    {
        $stored = $customPokemon->findByPokedexNumber($id);

        if (! $stored) {
            return redirect()
                ->route('pokemon.delete.index')
                ->with('status', 'Pokemon criado/fundido nao encontrado.');
        }

        $rules = $this->manualPokemonRules(false);
        $rules['remove_image'] = 'nullable|boolean';

        $validated = $request->validate($rules);
        $extra = $this->decodeExtraJson($validated['extra_json'] ?? null);

        if ($extra === false) {
            return back()
                ->withErrors(['extra_json' => 'O JSON extra nao esta valido.'])
                ->withInput();
        }

        $newImagePath = $this->storePokemonImage($request);
        $shouldRemoveStoredImage = $request->boolean('remove_image') || $newImagePath;
        $imagePath = $shouldRemoveStoredImage ? $newImagePath : ($stored['image_path'] ?? null);
        $imageUrl = $validated['image_url'] ?? null;
        $imageForRows = $imagePath ? $this->publicImageUrl($imagePath) : $imageUrl;

        $evolutions = $this->buildEvolutionRows(
            $validated['evolution_line'] ?? '',
            $validated['name'],
            $id,
            $imageForRows
        );

        $evolutions = array_map(function (array $evolution) use ($id, $validated, $imageForRows, $request, $newImagePath) {
            $isSelf = (int) ($evolution['id'] ?? 0) === $id
                || Str::lower($evolution['name'] ?? '') === Str::lower($validated['name']);

            if ($isSelf) {
                $evolution['id'] = $id;

                if ($imageForRows || $request->boolean('remove_image') || $newImagePath) {
                    $evolution['image'] = $imageForRows;
                }
            }

            return $evolution;
        }, $evolutions);

        if (filled($validated['existing_evolution_ref'] ?? null)) {
            $evolutions = $this->buildEvolutionRowsFromExisting(
                $validated['existing_evolution_ref'],
                $validated['name'],
                $id,
                $imageForRows,
                $customPokemon
            );
        }

        $meta = array_merge($stored['meta'] ?? [], [
            'rarity' => $validated['rarity'] ?? null,
            'color' => $validated['color'] ?? null,
            'habitat' => $validated['habitat'] ?? null,
            'shape' => $validated['shape'] ?? null,
            'growth_rate' => $validated['growth_rate'] ?? null,
            'capture_rate' => isset($validated['capture_rate']) ? (int) $validated['capture_rate'] : null,
            'is_legendary' => $request->boolean('is_legendary'),
            'is_mythical' => $request->boolean('is_mythical'),
            'is_baby' => $request->boolean('is_baby'),
            'extra' => $extra ?: [],
        ]);

        $updated = $customPokemon->updateByPokedexNumber($id, [
            'pokedex_number' => $id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'source' => $stored['source'] ?? 'manual',
            'generation' => $validated['generation'],
            'rarity' => $validated['rarity'] ?? null,
            'types' => array_values($validated['types']),
            'image_path' => $imagePath,
            'image_url' => $imageUrl,
            'height' => (float) $validated['height'],
            'weight' => (float) $validated['weight'],
            'base_experience' => (int) ($validated['base_experience'] ?? 0),
            'description' => $validated['description'],
            'abilities' => $this->linesFromText($validated['abilities'] ?? ''),
            'stats' => $this->normalizeInputStats($validated['stats'] ?? []),
            'moves' => $this->normalizeInputMoves($validated['moves'] ?? [], $validated['types'][0]),
            'evolutions' => $evolutions,
            'variants' => $this->buildVariantRows($validated['variants'] ?? '', $imageForRows),
            'locations' => $this->linesFromText($validated['locations'] ?? ''),
            'meta' => $meta,
            'fusion' => $stored['fusion'] ?? [],
            'ai_payload' => $stored['ai_payload'] ?? [],
        ]);

        if (! $updated) {
            return redirect()
                ->route('pokemon.delete.index')
                ->with('status', 'Nao foi possivel atualizar esse Pokemon.');
        }

        if ($shouldRemoveStoredImage && ! empty($stored['image_path']) && $stored['image_path'] !== $imagePath) {
            Storage::disk('public')->delete($stored['image_path']);
        }

        $this->syncCustomEvolutionLine($evolutions, $customPokemon);

        return redirect()
            ->route('pokemon.delete.index')
            ->with('status', 'Pokemon atualizado com sucesso.');
    }

    public function deleteSelectedCustomPokemon(Request $request, CustomPokemonRepository $customPokemon)
    {
        $validated = $request->validate([
            'ids' => 'nullable|array',
            'ids.*' => 'integer|min:10001',
            'delete_all' => 'nullable|boolean',
        ]);

        $allPokemon = $customPokemon->all();
        $ids = $request->boolean('delete_all')
            ? array_column($allPokemon, 'pokedex_number')
            : ($validated['ids'] ?? []);

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (! $ids) {
            return back()->with('status', 'Nenhum Pokemon selecionado para excluir.');
        }

        $toDelete = array_values(array_filter($allPokemon, fn ($pokemon) => in_array((int) ($pokemon['pokedex_number'] ?? 0), $ids, true)));
        $this->deleteStoredPokemonImages($toDelete);
        $deleted = $customPokemon->deleteByPokedexNumbers($ids);

        return redirect()
            ->route('pokemon.delete.index')
            ->with('status', "{$deleted} Pokemon excluido(s) com sucesso.");
    }

    public function destroyCustomPokemon(int $id, CustomPokemonRepository $customPokemon)
    {
        $stored = $customPokemon->findByPokedexNumber($id);

        if (! $stored) {
            return back()->with('status', 'Pokemon criado/fundido nao encontrado.');
        }

        $this->deleteStoredPokemonImages([$stored]);
        $customPokemon->deleteByPokedexNumber($id);

        return redirect()
            ->route('pokemon.delete.index')
            ->with('status', 'Pokemon excluido com sucesso.');
    }

    public function resetSamplePokemon(SamplePokemonSeeder $seeder)
    {
        $result = $seeder->resetAndSeed();

        return redirect()
            ->route('pokemon.list', ['origin' => 'custom'])
            ->with('success', "{$result['deleted']} Pokemon personalizados foram apagados e ".count($result['created']).' novos Pokemon foram criados.');
    }

    public function storeFusion(Request $request, CustomPokemonRepository $customPokemon)
    {
        $validated = $request->validate([
            'pokemon_a' => 'required|string',
            'pokemon_b' => 'required|string|different:pokemon_a',
            'name' => 'nullable|string|max:80',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'image_url' => 'nullable|url|max:2048',
            'use_ai' => 'nullable|boolean',
        ]);

        $first = $this->loadPokemonForFusion($validated['pokemon_a'], $customPokemon);
        $second = $this->loadPokemonForFusion($validated['pokemon_b'], $customPokemon);

        if (! $first || ! $second) {
            return back()
                ->withErrors(['pokemon_a' => 'Nao consegui carregar um dos Pokemon selecionados.'])
                ->withInput();
        }

        $aiData = [];
        $aiUsed = false;

        if ($request->boolean('use_ai') && filled(config('services.groq.key'))) {
            $aiData = $this->generateFusionWithGroq($first, $second);
            $aiUsed = ! empty($aiData);
        }

        $pokedexNumber = $customPokemon->nextPokedexNumber();
        $imagePath = $this->storePokemonImage($request);
        $payload = $this->buildFusionPokemonPayload(
            $first,
            $second,
            $validated,
            $pokedexNumber,
            $imagePath,
            $aiData,
            $aiUsed
        );

        $created = $customPokemon->create($payload);
        $message = $aiUsed
            ? 'Fusao criada com ajuda da IA e adicionada na Pokedex.'
            : 'Fusao criada com a logica local e adicionada na Pokedex.';

        return redirect()
            ->route('pokedex.show', ['id' => $created['pokedex_number']])
            ->with('success', $message);
    }

    public function search(Request $request, CustomPokemonRepository $customPokemon)
    {
        $query = strtolower(trim((string) $request->query('q', '')));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $results = [];

        foreach ($customPokemon->all() as $pokemon) {
            if (count($results) >= 10) {
                break;
            }

            $id = (int) ($pokemon['pokedex_number'] ?? 0);
            $name = strtolower($pokemon['name'] ?? '');

            if (
                (is_numeric($query) && str_starts_with((string) $id, $query))
                || (! is_numeric($query) && str_contains($name, $query))
            ) {
                $results[] = [
                    'id' => $id,
                    'name' => $this->displayName($pokemon['name'] ?? 'Pokemon criado'),
                    'source' => $pokemon['source'] ?? 'manual',
                ];
            }
        }

        if (count($results) < 10) {
            foreach ($this->apiPokemonIndex() as $poke) {
                if (count($results) >= 10) {
                    break;
                }

                $pokeName = $poke['name'];
                $id = $poke['id'];

                if (is_numeric($query)) {
                    if (str_starts_with((string) $id, $query)) {
                        $results[] = [
                            'id' => $id,
                            'name' => $this->displayName($pokeName),
                            'source' => 'api',
                        ];
                    }
                    continue;
                }

                if (str_contains($pokeName, $query)) {
                    $results[] = [
                        'id' => $id,
                        'name' => $this->displayName($pokeName),
                        'source' => 'api',
                    ];
                }
            }
        }

        return response()->json($results);
    }

    public function gamesHub()
    {
        return view('pokemon-games');
    }

    public function guessGame()
    {
        $correctId = rand(1, self::POKEAPI_LIMIT);
        $pokemon = $this->getApiPokemon($correctId);

        if (! $pokemon) {
            return "Erro ao buscar dados API";
        }

        $correctName = $this->displayName($pokemon['name']);
        $options = [$correctName];
        $usedIds = [$correctId];

        while (count($options) < 5) {
            $fakeId = rand(1, self::POKEAPI_LIMIT);
            if (in_array($fakeId, $usedIds, true)) {
                continue;
            }

            $fakePokemon = $this->getApiPokemon($fakeId);
            if ($fakePokemon) {
                $fakeName = $this->displayName($fakePokemon['name']);
                if (! in_array($fakeName, $options, true)) {
                    $options[] = $fakeName;
                    $usedIds[] = $fakeId;
                }
            }
        }

        shuffle($options);

        return view('jogo', compact('pokemon', 'options', 'correctName'));
    }

    public function fireRedGame(CustomPokemonRepository $customPokemon)
    {
        $customPokemonForGame = array_map(function (array $pokemon) use ($customPokemon) {
            $summary = $customPokemon->toFusionSummary($pokemon);

            return [
                'id' => (int) ($summary['id'] ?? 0),
                'name' => $summary['name'] ?? 'Pokemon criado',
                'types' => array_values($summary['types'] ?? ['normal']),
                'image' => $summary['image_url'] ?? null,
                'stats' => $summary['stats'] ?? [],
                'moves' => array_slice(array_values($summary['moves'] ?? []), 0, 4),
                'generation' => $summary['generation'] ?? 'custom',
                'isCustom' => true,
            ];
        }, $customPokemon->all());

        return view('pokemon-fire-red', [
            'customPokemonForGame' => $customPokemonForGame,
        ]);
    }

    public function listarGeradosPorIA(CustomPokemonRepository $customPokemon)
    {
        $generated = array_values(array_filter($customPokemon->all(), function ($pokemon) {
            return ($pokemon['source'] ?? '') === 'ai'
                || (($pokemon['source'] ?? '') === 'fusion' && ! empty($pokemon['fusion']['ai_used']))
                || ! empty($pokemon['meta']['ai_used']);
        }));

        return response()->json($generated);
    }

    public function porTipo(string $tipo, CustomPokemonRepository $customPokemon)
    {
        $tipo = strtolower($tipo);
        $cards = [];

        foreach ($customPokemon->all() as $pokemon) {
            if (in_array($tipo, array_map('strtolower', $pokemon['types'] ?? []), true)) {
                $cards[] = $customPokemon->toListCard($pokemon);
            }
        }

        foreach (array_slice($this->apiPokemonIdsByType($tipo), 0, 50) as $id) {
            $cards[] = $this->buildApiListCard(['source' => 'api', 'id' => $id, 'name' => '']);
        }

        return response()->json($cards);
    }

    public function lendarios(CustomPokemonRepository $customPokemon)
    {
        $legendary = array_values(array_filter($customPokemon->all(), function ($pokemon) {
            return ! empty($pokemon['meta']['is_legendary']);
        }));

        return response()->json($legendary);
    }

    public function detalhes($id, CustomPokemonRepository $customPokemon)
    {
        $stored = $customPokemon->findByPokedexNumber($id);
        if ($stored) {
            return response()->json($customPokemon->toPokedexViewData($stored));
        }

        $pokemon = $this->getApiPokemon($id);
        if (! $pokemon) {
            return response()->json(['erro' => 'Pokemon nao encontrado'], 404);
        }

        return response()->json($pokemon);
    }

    public function criar(Request $request, CustomPokemonRepository $customPokemon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'types' => 'required|array|min:1|max:2',
            'types.*' => 'required|string',
            'description' => 'required|string|max:1000',
        ]);

        $number = $customPokemon->nextPokedexNumber();
        $created = $customPokemon->create([
            'pokedex_number' => $number,
            'name' => $validated['name'],
            'source' => 'manual',
            'generation' => 'custom',
            'types' => array_slice($validated['types'], 0, 2),
            'image_url' => $request->input('image_url'),
            'height' => (float) $request->input('height', 10),
            'weight' => (float) $request->input('weight', 10),
            'base_experience' => (int) $request->input('base_experience', 0),
            'description' => $validated['description'],
            'abilities' => (array) $request->input('abilities', []),
            'stats' => $this->normalizeInputStats((array) $request->input('stats', [])),
            'moves' => (array) $request->input('moves', []),
            'evolutions' => [[
                'id' => $number,
                'name' => $validated['name'],
                'image' => $request->input('image_url'),
            ]],
            'locations' => (array) $request->input('locations', []),
            'variants' => [],
            'meta' => [],
        ]);

        return response()->json($created, 201);
    }

    public function atualizar(Request $request, int $id, CustomPokemonRepository $customPokemon)
    {
        $updated = $customPokemon->updateByPokedexNumber($id, $request->all());

        if (! $updated) {
            return response()->json(['erro' => 'Pokemon criado nao encontrado'], 404);
        }

        return response()->json($updated);
    }

    public function excluir(int $id, CustomPokemonRepository $customPokemon)
    {
        if (! $customPokemon->deleteByPokedexNumber($id)) {
            return response()->json(['erro' => 'Pokemon criado nao encontrado'], 404);
        }

        return response()->json(['mensagem' => 'Pokemon excluido com sucesso']);
    }

    private function filterApiRefs(array $filters): array
    {
        if ($filters['generation'] === 'custom') {
            return [];
        }

        $refs = $this->apiPokemonIndex();
        $query = strtolower($filters['q']);

        if ($query !== '') {
            $refs = array_values(array_filter($refs, function ($pokemon) use ($query) {
                if (is_numeric($query)) {
                    return str_starts_with((string) $pokemon['id'], $query);
                }

                return str_contains($pokemon['name'], $query);
            }));
        }

        if ($filters['generation'] !== '' && isset(self::GENERATIONS[$filters['generation']])) {
            $range = self::GENERATIONS[$filters['generation']];
            $refs = array_values(array_filter($refs, function ($pokemon) use ($range) {
                return $pokemon['id'] >= $range['from'] && $pokemon['id'] <= $range['to'];
            }));
        }

        if ($filters['type'] !== '' && in_array($filters['type'], self::TYPES, true)) {
            $idsByType = array_flip($this->apiPokemonIdsByType($filters['type']));
            $refs = array_values(array_filter($refs, fn ($pokemon) => isset($idsByType[$pokemon['id']])));
        }

        return array_map(fn ($pokemon) => [
            'source' => 'api',
            'id' => (int) $pokemon['id'],
            'name' => $pokemon['name'],
        ], $refs);
    }

    private function filterCustomRefs(array $filters, array $customPokemon): array
    {
        if ($filters['generation'] !== '' && $filters['generation'] !== 'custom' && ! isset(self::GENERATIONS[$filters['generation']])) {
            return [];
        }

        $query = strtolower($filters['q']);
        $origin = $filters['origin'];

        $filtered = array_values(array_filter($customPokemon, function ($pokemon) use ($filters, $query, $origin) {
            if (in_array($origin, ['manual', 'fusion', 'ai'], true) && ($pokemon['source'] ?? '') !== $origin) {
                return false;
            }

            if ($filters['generation'] === 'custom') {
                // Todos os criados entram nesse grupo especial.
            } elseif ($filters['generation'] !== '' && (string) ($pokemon['generation'] ?? '') !== $filters['generation']) {
                return false;
            }

            if ($filters['type'] !== '' && ! in_array($filters['type'], array_map('strtolower', $pokemon['types'] ?? []), true)) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $id = (string) ($pokemon['pokedex_number'] ?? '');
            $name = strtolower($pokemon['name'] ?? '');

            return is_numeric($query)
                ? str_starts_with($id, $query)
                : str_contains($name, $query);
        }));

        return array_map(fn ($pokemon) => [
            'source' => 'custom',
            'id' => (int) ($pokemon['pokedex_number'] ?? 0),
            'name' => strtolower($pokemon['name'] ?? ''),
        ], $filtered);
    }

    private function sortRefs(array &$refs, string $sort): void
    {
        usort($refs, function ($a, $b) use ($sort) {
            return match ($sort) {
                'number_desc' => $b['id'] <=> $a['id'],
                'name_asc' => strcmp($a['name'], $b['name']),
                'name_desc' => strcmp($b['name'], $a['name']),
                default => $a['id'] <=> $b['id'],
            };
        });
    }

    private function buildListCard(array $ref, CustomPokemonRepository $customPokemon): array
    {
        if ($ref['source'] === 'custom') {
            $stored = $customPokemon->findByPokedexNumber($ref['id']);

            return $stored
                ? $customPokemon->toListCard($stored)
                : $this->fallbackListCard($ref);
        }

        return $this->buildApiListCard($ref);
    }

    private function buildApiListCard(array $ref): array
    {
        $pokemon = $this->getApiPokemon($ref['id']);

        if (! $pokemon) {
            return $this->fallbackListCard($ref);
        }

        $species = $this->getApiSpecies((int) $pokemon['id']) ?? [];
        $evolutionCount = $this->evolutionCountFromSpecies($species);
        $image = $pokemon['sprites']['other']['official-artwork']['front_default']
            ?? $pokemon['sprites']['front_default']
            ?? 'https://placehold.co/320x320/0f172a/facc15?text=?';

        return [
            'source' => 'api',
            'id' => (int) $pokemon['id'],
            'name' => $this->displayName($pokemon['name']),
            'image' => $image,
            'types' => array_map(fn ($entry) => $entry['type']['name'] ?? 'normal', $pokemon['types'] ?? []),
            'generation' => $this->generationLabelForId((int) $pokemon['id']),
            'evolution_count' => $evolutionCount,
            'is_custom' => false,
            'detail_url' => route('pokedex.show', ['id' => (int) $pokemon['id']]),
        ];
    }

    private function fallbackListCard(array $ref): array
    {
        return [
            'source' => $ref['source'],
            'id' => (int) ($ref['id'] ?? 0),
            'name' => $this->displayName($ref['name'] ?? 'Pokemon'),
            'image' => 'https://placehold.co/320x320/0f172a/facc15?text=?',
            'types' => [],
            'generation' => 'Indefinida',
            'evolution_count' => 0,
            'is_custom' => $ref['source'] === 'custom',
            'detail_url' => route('pokedex.show', ['id' => (int) ($ref['id'] ?? 0)]),
        ];
    }

    private function apiPokemonIndex(): array
    {
        $data = $this->pokeApiGet('pokemon', ['limit' => self::POKEAPI_LIMIT], 'pokemon-index', 24) ?? [];
        $results = $data['results'] ?? [];

        return array_values(array_filter(array_map(function ($pokemon) {
            $id = $this->extractIdFromUrl($pokemon['url'] ?? '');

            if (! $id || $id > self::POKEAPI_LIMIT) {
                return null;
            }

            return [
                'id' => $id,
                'name' => $pokemon['name'],
                'url' => $pokemon['url'],
            ];
        }, $results)));
    }

    private function apiPokemonIdsByType(string $type): array
    {
        if (! in_array($type, self::TYPES, true)) {
            return [];
        }

        $data = $this->pokeApiGet("type/{$type}", [], 'pokemon-type-'.$type, 24) ?? [];

        $ids = array_values(array_filter(array_map(function ($entry) {
            $id = $this->extractIdFromUrl($entry['pokemon']['url'] ?? '');

            return $id && $id <= self::POKEAPI_LIMIT ? $id : null;
        }, $data['pokemon'] ?? [])));

        sort($ids);

        return $ids;
    }

    private function getApiPokemon($idOrName): ?array
    {
        $key = Str::slug((string) $idOrName);

        return $this->pokeApiGet("pokemon/{$idOrName}", [], 'pokemon-'.$key, 48);
    }

    private function getApiSpecies(int $id): ?array
    {
        return $this->pokeApiGet("pokemon-species/{$id}", [], 'pokemon-species-'.$id, 72);
    }

    private function getEvolutionChain(string $url): ?array
    {
        $chainId = $this->extractIdFromUrl($url) ?: md5($url);

        return $this->pokeApiGet($url, [], 'evolution-chain-'.$chainId, 168);
    }

    private function pokeApiGet(string $endpoint, array $query = [], ?string $cacheKey = null, int $ttlHours = 24): ?array
    {
        $fetch = function () use ($endpoint, $query) {
            $url = str_starts_with($endpoint, 'http')
                ? $endpoint
                : 'https://pokeapi.co/api/v2/'.ltrim($endpoint, '/');

            $response = Http::timeout(12)->retry(1, 200)->get($url, $query);

            return $response->successful() ? $response->json() : null;
        };

        if (! $cacheKey) {
            return $fetch();
        }

        return Cache::store('file')->remember('pokeapi-'.$cacheKey, now()->addHours($ttlHours), $fetch);
    }

    private function evolutionCountFromSpecies(array $species): int
    {
        if (empty($species['evolution_chain']['url'])) {
            return 0;
        }

        $chain = $this->getEvolutionChain($species['evolution_chain']['url']);
        if (! $chain || empty($chain['chain'])) {
            return 0;
        }

        return max(0, count($this->parseEvolutionChain($chain['chain'])) - 1);
    }

    private function parseEvolutionChain(array $chain): array
    {
        $species = [[
            'name' => $chain['species']['name'],
            'url' => $chain['species']['url'] ?? '',
        ]];

        foreach ($chain['evolves_to'] ?? [] as $next) {
            $species = array_merge($species, $this->parseEvolutionChain($next));
        }

        return $species;
    }

    private function buildEvolutionDetails(array $speciesEntries): array
    {
        return array_map(function ($entry) {
            $name = $this->displayName($entry['name']);
            $id = $this->extractIdFromUrl($entry['url']);
            $image = null;

            $pokemonData = $this->getApiPokemon($entry['name']);
            if ($pokemonData) {
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

            $pokemonData = $this->pokeApiGet(
                $variety['pokemon']['url'],
                [],
                'pokemon-variant-'.$this->extractIdFromUrl($variety['pokemon']['url']),
                72
            );

            if (! $pokemonData) {
                continue;
            }

            $variantName = $variety['pokemon']['name'] ?? '';
            $formName = strpos($variantName, '-') !== false
                ? $this->displayName($variantName)
                : 'Normal';

            $variants[] = [
                'id' => $pokemonData['id'] ?? null,
                'name' => $this->displayName($variantName),
                'form' => $formName,
                'image' => $pokemonData['sprites']['other']['official-artwork']['front_default']
                    ?? $pokemonData['sprites']['front_default']
                    ?? null,
                'is_current' => ($pokemonData['id'] ?? null) === $currentId,
            ];
        }

        return $variants;
    }

    private function buildMoveDetails(array $pokemonMoves): array
    {
        $moves = [];

        foreach (array_slice($pokemonMoves, 0, 8) as $entry) {
            if (empty($entry['move']['url'])) {
                continue;
            }

            $moveData = $this->pokeApiGet(
                $entry['move']['url'],
                [],
                'move-'.$this->extractIdFromUrl($entry['move']['url']),
                168
            );

            if (! $moveData) {
                continue;
            }

            $effectEntry = collect($moveData['effect_entries'] ?? [])->first(function ($item) {
                return isset($item['language']['name']) && $item['language']['name'] === 'en';
            });

            $effect = $effectEntry['short_effect'] ?? $effectEntry['effect'] ?? 'Descricao nao disponivel.';
            $effect = str_replace(["\n", "\r", "\f"], ' ', $effect);
            $effect = $this->translateText($effect, 'en', 'pt');

            $moves[] = [
                'name' => $this->displayName($moveData['name']),
                'type' => $this->displayName($moveData['type']['name'] ?? '---'),
                'category' => $this->displayName($moveData['damage_class']['name'] ?? '---'),
                'power' => $moveData['power'] ?? '-',
                'accuracy' => $moveData['accuracy'] ?? '-',
                'pp' => $moveData['pp'] ?? '-',
                'effect' => $effect,
            ];
        }

        return $moves;
    }

    private function manualPokemonRules(bool $useAi = false): array
    {
        $typeRule = 'required|string|in:'.implode(',', self::TYPES);
        $rarityRule = 'required|string|in:'.implode(',', array_keys(self::RARITIES));

        $rules = [
            'name' => 'required|string|max:80',
            'pokedex_number' => 'nullable|integer|min:10001|max:999999',
            'generation' => 'required|string|in:1,2,3,4,5,6,7,8,9,custom',
            'rarity' => $rarityRule,
            'types' => 'required|array|min:1|max:2',
            'types.*' => $typeRule,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'image_url' => 'nullable|url|max:2048',
            'height' => 'required|numeric|min:0|max:9999',
            'weight' => 'required|numeric|min:0|max:99999',
            'base_experience' => 'nullable|integer|min:0|max:999999',
            'description' => 'required|string|max:1200',
            'abilities' => 'nullable|string|max:1200',
            'stats' => 'required|array',
            'stats.hp' => 'required|integer|min:1|max:999',
            'stats.attack' => 'required|integer|min:1|max:999',
            'stats.defense' => 'required|integer|min:1|max:999',
            'stats.special_attack' => 'required|integer|min:1|max:999',
            'stats.special_defense' => 'required|integer|min:1|max:999',
            'stats.speed' => 'required|integer|min:1|max:999',
            'moves' => 'nullable|array',
            'moves.*.name' => 'nullable|string|max:80',
            'moves.*.type' => 'nullable|string|in:'.implode(',', self::TYPES),
            'moves.*.category' => 'nullable|string|max:40',
            'moves.*.power' => 'nullable|integer|min:0|max:999',
            'moves.*.accuracy' => 'nullable|integer|min:0|max:100',
            'moves.*.pp' => 'nullable|integer|min:0|max:999',
            'moves.*.effect' => 'nullable|string|max:500',
            'existing_evolution_ref' => 'nullable|string|max:80',
            'evolution_line' => 'nullable|string|max:1500',
            'locations' => 'nullable|string|max:1500',
            'variants' => 'nullable|string|max:1500',
            'color' => 'nullable|string|max:40',
            'habitat' => 'nullable|string|max:80',
            'shape' => 'nullable|string|max:80',
            'growth_rate' => 'nullable|string|max:80',
            'capture_rate' => 'nullable|integer|min:0|max:255',
            'is_legendary' => 'nullable|boolean',
            'is_mythical' => 'nullable|boolean',
            'is_baby' => 'nullable|boolean',
            'extra_json' => 'nullable|string|max:5000',
        ];

        if (! $useAi) {
            return $rules;
        }

        foreach ([
            'height',
            'weight',
            'base_experience',
            'description',
            'stats',
            'stats.hp',
            'stats.attack',
            'stats.defense',
            'stats.special_attack',
            'stats.special_defense',
            'stats.speed',
        ] as $field) {
            $rules[$field] = str_replace('required', 'nullable', $rules[$field]);
        }

        return $rules;
    }

    private function decodeExtraJson(?string $json): array|false
    {
        if (! filled($json)) {
            return [];
        }

        $decoded = json_decode($json, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
            ? $decoded
            : false;
    }

    private function normalizeInputStats(array $stats): array
    {
        return [
            'hp' => (int) ($stats['hp'] ?? 45),
            'attack' => (int) ($stats['attack'] ?? 45),
            'defense' => (int) ($stats['defense'] ?? 45),
            'special-attack' => (int) ($stats['special_attack'] ?? $stats['special-attack'] ?? 45),
            'special-defense' => (int) ($stats['special_defense'] ?? $stats['special-defense'] ?? 45),
            'speed' => (int) ($stats['speed'] ?? 45),
        ];
    }

    private function normalizeInputMoves(array $moves, string $fallbackType = 'normal'): array
    {
        $normalized = [];

        foreach ($moves as $move) {
            if (empty($move['name'])) {
                continue;
            }

            $normalized[] = [
                'name' => $move['name'],
                'type' => $move['type'] ?? $fallbackType,
                'category' => $move['category'] ?? 'status',
                'power' => $move['power'] ?? '-',
                'accuracy' => $move['accuracy'] ?? '-',
                'pp' => $move['pp'] ?? '-',
                'effect' => $move['effect'] ?? 'Ataque criado para este Pokemon.',
            ];
        }

        return $normalized;
    }

    private function linesFromText(?string $text): array
    {
        if (! filled($text)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
    }

    private function buildEvolutionRows(?string $text, string $selfName, int $selfNumber, ?string $image): array
    {
        $lines = $this->linesFromText($text);

        if (! $lines) {
            return [[
                'id' => $selfNumber,
                'name' => $selfName,
                'image' => $image,
            ]];
        }

        return array_map(function ($line) use ($selfName, $selfNumber, $image) {
            $parts = array_map('trim', explode('|', $line));
            $id = null;
            $name = $parts[0] ?? $line;
            $rowImage = $parts[1] ?? null;

            if (isset($parts[1]) && is_numeric($parts[0])) {
                $id = (int) $parts[0];
                $name = $parts[1];
                $rowImage = $parts[2] ?? null;
            }

            if (Str::lower($name) === Str::lower($selfName)) {
                $id = $selfNumber;
                $rowImage = $rowImage ?: $image;
            }

            return [
                'id' => $id,
                'name' => $name,
                'image' => $rowImage,
            ];
        }, $lines);
    }

    private function buildEvolutionRowsFromExisting(
        string $ref,
        string $selfName,
        int $selfNumber,
        ?string $image,
        CustomPokemonRepository $customPokemon
    ): array {
        [$source, $id] = array_pad(explode(':', $ref, 2), 2, null);
        $rows = [];

        if ($source === 'custom' && is_numeric($id)) {
            $stored = $customPokemon->findByPokedexNumber($id);
            if ($stored) {
                $rows = $customPokemon->toPokedexViewData($stored)['evolutions'] ?? [];
            }
        }

        if ($source === 'api' && is_numeric($id)) {
            $species = $this->getApiSpecies((int) $id) ?? [];
            if (! empty($species['evolution_chain']['url'])) {
                $chain = $this->getEvolutionChain($species['evolution_chain']['url']);
                if ($chain && ! empty($chain['chain'])) {
                    $rows = $this->buildEvolutionDetails($this->parseEvolutionChain($chain['chain']));
                }
            }

            if (! $rows) {
                $pokemon = $this->getApiPokemon((int) $id);
                if ($pokemon) {
                    $rows[] = [
                        'id' => (int) ($pokemon['id'] ?? $id),
                        'name' => $this->displayName($pokemon['name'] ?? 'Pokemon'),
                        'image' => $pokemon['sprites']['other']['official-artwork']['front_default']
                            ?? $pokemon['sprites']['front_default']
                            ?? null,
                    ];
                }
            }
        }

        $rows = array_values(array_filter(array_map(function ($row) {
            if (! is_array($row) || empty($row['name'])) {
                return null;
            }

            return [
                'id' => $row['id'] ?? null,
                'name' => $row['name'],
                'image' => $row['image'] ?? null,
            ];
        }, $rows)));

        $hasSelf = collect($rows)->contains(function ($row) use ($selfName, $selfNumber) {
            return (int) ($row['id'] ?? 0) === $selfNumber
                || Str::lower($row['name'] ?? '') === Str::lower($selfName);
        });

        if (! $hasSelf) {
            $rows[] = [
                'id' => $selfNumber,
                'name' => $selfName,
                'image' => $image,
            ];
        }

        return $rows ?: [[
            'id' => $selfNumber,
            'name' => $selfName,
            'image' => $image,
        ]];
    }

    private function syncCustomEvolutionLine(array $evolutions, CustomPokemonRepository $customPokemon): void
    {
        $customIds = array_values(array_filter(array_map(function ($evolution) {
            $id = (int) ($evolution['id'] ?? 0);

            return $id > self::POKEAPI_LIMIT ? $id : null;
        }, $evolutions)));

        if ($customIds) {
            $customPokemon->updateEvolutionLineForNumbers($customIds, $evolutions);
        }
    }

    private function buildVariantRows(?string $text, ?string $image): array
    {
        return array_map(function ($line) use ($image) {
            return [
                'id' => null,
                'name' => $line,
                'form' => $line,
                'image' => $image,
                'is_current' => false,
            ];
        }, $this->linesFromText($text));
    }

    private function pokemonSelectionOptions(CustomPokemonRepository $customPokemon): array
    {
        $options = array_map(function ($pokemon) {
            return [
                'value' => 'api:'.$pokemon['id'],
                'label' => '#'.str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT).' '.$this->displayName($pokemon['name']),
                'group' => 'PokeAPI',
            ];
        }, $this->apiPokemonIndex());

        foreach ($customPokemon->all() as $pokemon) {
            $options[] = [
                'value' => 'custom:'.($pokemon['pokedex_number'] ?? ''),
                'label' => '#'.($pokemon['pokedex_number'] ?? '').' '.$this->displayName($pokemon['name'] ?? 'Pokemon criado'),
                'group' => ($pokemon['source'] ?? '') === 'fusion' ? 'Fusions criadas' : 'Criados por voce',
            ];
        }

        return $options;
    }

    private function loadPokemonForFusion(string $ref, CustomPokemonRepository $customPokemon): ?array
    {
        [$source, $id] = array_pad(explode(':', $ref, 2), 2, null);

        if ($source === 'custom') {
            $stored = $customPokemon->findByPokedexNumber($id);

            return $stored ? $customPokemon->toFusionSummary($stored) : null;
        }

        if ($source !== 'api' || ! is_numeric($id)) {
            return null;
        }

        $pokemon = $this->getApiPokemon((int) $id);
        if (! $pokemon) {
            return null;
        }

        $species = $this->getApiSpecies((int) $id) ?? [];
        $stats = [];
        foreach ($pokemon['stats'] ?? [] as $stat) {
            $stats[$stat['stat']['name'] ?? 'hp'] = (int) ($stat['base_stat'] ?? 45);
        }

        return [
            'ref' => $ref,
            'id' => (int) $pokemon['id'],
            'name' => $this->displayName($pokemon['name']),
            'types' => array_map(fn ($entry) => $entry['type']['name'] ?? 'normal', $pokemon['types'] ?? []),
            'height' => (float) ($pokemon['height'] ?? 0),
            'weight' => (float) ($pokemon['weight'] ?? 0),
            'image_url' => $pokemon['sprites']['other']['official-artwork']['front_default']
                ?? $pokemon['sprites']['front_default']
                ?? null,
            'description' => $species
                ? $this->translateText($this->extractEnglishText($species, 'flavor_text_entries', 'flavor_text', 'language', 'name'))
                : '',
            'abilities' => array_map(fn ($entry) => $entry['ability']['name'] ?? '', $pokemon['abilities'] ?? []),
            'moves' => array_map(fn ($entry) => $entry['move']['name'] ?? '', array_slice($pokemon['moves'] ?? [], 0, 8)),
            'stats' => $stats,
            'generation' => $this->generationKeyForId((int) $pokemon['id']),
        ];
    }

    private function generateFusionWithGroq(array $first, array $second): array
    {
        $apiKey = config('services.groq.key');

        if (! filled($apiKey)) {
            return [];
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'temperature' => 0.85,
                'max_completion_tokens' => 1800,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Voce cria dados fanmade para uma fusao de Pokemon. Responda somente JSON valido, sem markdown.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'instruction' => 'Crie uma fusao equilibrada em portugues. Use no maximo 2 tipos em ingles da PokeAPI. Campos obrigatorios: name, description, types, abilities, stats, moves, color, habitat, shape, growth_rate, image_svg. image_svg deve ser uma ilustracao SVG 320x320 simples, segura, sem script, sem foreignObject, sem links externos, inspirada visualmente nos dois Pokemon.',
                            'allowed_types' => self::TYPES,
                            'stats_keys' => ['hp', 'attack', 'defense', 'special-attack', 'special-defense', 'speed'],
                            'move_shape' => ['name', 'type', 'category', 'power', 'accuracy', 'pp', 'effect'],
                            'image_svg_rules' => [
                                'root_svg_only',
                                'viewBox 0 0 320 320',
                                'inline shapes only',
                                'no script',
                                'no foreignObject',
                                'no external images',
                            ],
                            'pokemon_a' => $first,
                            'pokemon_b' => $second,
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ]);

        if (! $response->successful()) {
            return [];
        }

        $content = data_get($response->json(), 'choices.0.message.content', '');

        return $this->decodeJsonFromText($content);
    }

    private function decodeJsonFromText(?string $text): array
    {
        if (! filled($text)) {
            return [];
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);

            return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }

    private function generateCreationWithGroq(string $name, array $types, string $rarity): array
    {
        $apiKey = config('services.groq.key');

        if (! filled($apiKey)) {
            return [];
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'temperature' => 0.85,
                'max_completion_tokens' => 1800,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Voce cria fichas fanmade de Pokemon em portugues. Responda somente JSON valido, sem markdown.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'instruction' => 'Crie o formato completo de um Pokemon original usando o nome, tipos e raridade enviados pelo usuario. Preserve o nome e os tipos. Responda com: name, description, generation, rarity, height, weight, base_experience, types, abilities, stats, moves, evolutions, variants, locations, color, habitat, shape, growth_rate, capture_rate, is_legendary, is_mythical, is_baby, image_svg. image_svg deve ser uma ilustracao SVG 320x320 simples, segura, sem script, sem foreignObject, sem links externos, coerente com nome, tipos e raridade.',
                            'name' => $name,
                            'types' => array_values($types),
                            'rarity' => $rarity,
                            'allowed_types' => self::TYPES,
                            'rarity_options' => array_keys(self::RARITIES),
                            'stats_keys' => ['hp', 'attack', 'defense', 'special-attack', 'special-defense', 'speed'],
                            'move_shape' => ['name', 'type', 'category', 'power', 'accuracy', 'pp', 'effect'],
                            'evolution_shape' => ['name'],
                            'image_svg_rules' => [
                                'root_svg_only',
                                'viewBox 0 0 320 320',
                                'inline shapes only',
                                'no script',
                                'no foreignObject',
                                'no external images',
                            ],
                            'language' => 'pt-BR',
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ]);

        if (! $response->successful()) {
            return [];
        }

        $content = data_get($response->json(), 'choices.0.message.content', '');

        return $this->decodeJsonFromText($content);
    }

    private function buildAiCreatedPokemonPayload(
        array $validated,
        int $pokedexNumber,
        ?string $imagePath,
        ?string $imageUrl,
        array $aiData,
        bool $aiUsed
    ): array {
        $types = $this->sanitizeTypes($validated['types'] ?? []);
        if (! $imagePath && ! $imageUrl) {
            $imagePath = $this->storeGeneratedPokemonSvg(
                $aiData['image_svg'] ?? null,
                $validated['name'],
                $pokedexNumber,
                $types
            );
        }

        $stats = $this->sanitizeStats($aiData['stats'] ?? [])
            ?: $this->rarityStats($validated['rarity']);
        $moves = $this->sanitizeMoves($aiData['moves'] ?? [], $types)
            ?: $this->defaultMovesForTypes($types);
        $imageForRows = $imagePath ? $this->publicImageUrl($imagePath) : $imageUrl;
        $description = $aiData['description']
            ?? "Pokemon {$validated['name']} criado com tipagem ".implode('/', $types)." e raridade ".self::RARITIES[$validated['rarity']].'.';

        $evolutions = $this->sanitizeEvolutions(
            $aiData['evolutions'] ?? [],
            $validated['name'],
            $pokedexNumber,
            $imageForRows
        );

        return [
            'pokedex_number' => $pokedexNumber,
            'name' => $validated['name'],
            'source' => 'ai',
            'generation' => $aiData['generation'] ?? ($validated['generation'] ?? 'custom'),
            'rarity' => $validated['rarity'],
            'types' => $types,
            'image_path' => $imagePath,
            'image_url' => $imageUrl,
            'height' => isset($aiData['height']) && is_numeric($aiData['height']) ? (float) $aiData['height'] : 10,
            'weight' => isset($aiData['weight']) && is_numeric($aiData['weight']) ? (float) $aiData['weight'] : 30,
            'base_experience' => isset($aiData['base_experience']) && is_numeric($aiData['base_experience'])
                ? (int) $aiData['base_experience']
                : (int) round(array_sum($stats) / 6),
            'description' => $description,
            'abilities' => $this->sanitizeStringList($aiData['abilities'] ?? [])
                ?: ['Adaptabilidade '.$this->displayName($types[0] ?? 'normal')],
            'stats' => $stats,
            'moves' => $moves,
            'evolutions' => $evolutions,
            'variants' => $this->sanitizeVariants($aiData['variants'] ?? [], $imageForRows),
            'locations' => $this->sanitizeStringList($aiData['locations'] ?? [])
                ?: ['Regiao criada pelo usuario'],
            'meta' => [
                'rarity' => $validated['rarity'],
                'rarity_label' => self::RARITIES[$validated['rarity']] ?? $validated['rarity'],
                'color' => $aiData['color'] ?? null,
                'habitat' => $aiData['habitat'] ?? null,
                'shape' => $aiData['shape'] ?? null,
                'growth_rate' => $aiData['growth_rate'] ?? null,
                'capture_rate' => isset($aiData['capture_rate']) && is_numeric($aiData['capture_rate']) ? (int) $aiData['capture_rate'] : null,
                'is_legendary' => (bool) ($aiData['is_legendary'] ?? $validated['rarity'] === 'legendary'),
                'is_mythical' => (bool) ($aiData['is_mythical'] ?? $validated['rarity'] === 'mythical'),
                'is_baby' => (bool) ($aiData['is_baby'] ?? false),
                'ai_used' => $aiUsed,
                'model' => $aiUsed ? config('services.groq.model') : null,
                'extra' => [
                    'groq_response' => $aiData,
                ],
            ],
            'ai_payload' => $aiData,
        ];
    }

    private function buildFusionPokemonPayload(
        array $first,
        array $second,
        array $validated,
        int $pokedexNumber,
        ?string $imagePath,
        array $aiData,
        bool $aiUsed
    ): array {
        $types = $this->sanitizeTypes($aiData['types'] ?? []);
        if (! $types) {
            $types = array_slice(array_values(array_unique(array_merge($first['types'], $second['types']))), 0, 2);
        }

        if (! $imagePath && empty($validated['image_url']) && ! empty($aiData)) {
            $imagePath = $this->storeGeneratedPokemonSvg(
                $aiData['image_svg'] ?? null,
                $aiData['name'] ?? $this->blendNames($first['name'], $second['name']),
                $pokedexNumber,
                $types
            );
        }

        $stats = $this->sanitizeStats($aiData['stats'] ?? []);
        if (! $stats) {
            $stats = $this->averageStats($first['stats'], $second['stats']);
        }

        $name = trim($validated['name'] ?? '')
            ?: ($aiData['name'] ?? $this->blendNames($first['name'], $second['name']));

        $image = $validated['image_url']
            ?? $aiData['image_url']
            ?? $first['image_url']
            ?? $second['image_url']
            ?? null;
        $evolutionImage = $imagePath ? $this->publicImageUrl($imagePath) : $image;

        return [
            'pokedex_number' => $pokedexNumber,
            'name' => $name,
            'source' => 'fusion',
            'generation' => 'custom',
            'types' => $types ?: ['normal'],
            'image_path' => $imagePath,
            'image_url' => $image,
            'height' => round(((float) $first['height'] + (float) $second['height']) / 2, 1),
            'weight' => round(((float) $first['weight'] + (float) $second['weight']) / 2, 1),
            'base_experience' => (int) round(array_sum($stats) / 6),
            'description' => $aiData['description']
                ?? "Uma fusao entre {$first['name']} e {$second['name']}, combinando tracos, poderes e comportamento dos dois.",
            'abilities' => $this->sanitizeStringList($aiData['abilities'] ?? [])
                ?: array_slice(array_values(array_unique(array_filter(array_merge($first['abilities'], $second['abilities'])))), 0, 3),
            'stats' => $stats,
            'moves' => $this->sanitizeMoves($aiData['moves'] ?? [], $types)
                ?: $this->fallbackFusionMoves($first, $second, $types[0] ?? 'normal'),
            'evolutions' => [[
                'id' => $pokedexNumber,
                'name' => $name,
                'image' => $evolutionImage,
            ]],
            'variants' => [],
            'locations' => ['Laboratorio de fusao'],
            'meta' => [
                'color' => $aiData['color'] ?? null,
                'habitat' => $aiData['habitat'] ?? 'laboratorio',
                'shape' => $aiData['shape'] ?? 'fusion',
                'growth_rate' => $aiData['growth_rate'] ?? 'medium',
                'capture_rate' => 45,
                'is_legendary' => false,
                'is_mythical' => false,
                'is_baby' => false,
                'extra' => [],
            ],
            'fusion' => [
                'parents' => [$first, $second],
                'ai_used' => $aiUsed,
                'model' => $aiUsed ? config('services.groq.model') : null,
            ],
            'ai_payload' => $aiData,
        ];
    }

    private function sanitizeTypes($types): array
    {
        if (! is_array($types)) {
            return [];
        }

        return array_slice(array_values(array_unique(array_filter(array_map(function ($type) {
            $type = strtolower((string) $type);

            return in_array($type, self::TYPES, true) ? $type : null;
        }, $types)))), 0, 2);
    }

    private function sanitizeStats($stats): array
    {
        if (! is_array($stats)) {
            return [];
        }

        $normalized = [];
        foreach (['hp', 'attack', 'defense', 'special-attack', 'special-defense', 'speed'] as $key) {
            $value = $stats[$key] ?? $stats[str_replace('-', '_', $key)] ?? null;
            if ($value === null || ! is_numeric($value)) {
                return [];
            }
            $normalized[$key] = min(255, max(1, (int) $value));
        }

        return $normalized;
    }

    private function averageStats(array $first, array $second): array
    {
        $stats = [];

        foreach (['hp', 'attack', 'defense', 'special-attack', 'special-defense', 'speed'] as $key) {
            $a = (int) ($first[$key] ?? 45);
            $b = (int) ($second[$key] ?? 45);
            $stats[$key] = min(255, max(1, (int) round((($a + $b) / 2) + 8)));
        }

        return $stats;
    }

    private function sanitizeStringList($items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(fn ($item) => is_string($item) ? trim($item) : null, $items)));
    }

    private function sanitizeMoves($moves, array $types): array
    {
        if (! is_array($moves)) {
            return [];
        }

        $normalized = [];
        foreach ($moves as $move) {
            if (! is_array($move) || empty($move['name'])) {
                continue;
            }

            $normalized[] = [
                'name' => $move['name'],
                'type' => in_array(strtolower($move['type'] ?? ''), self::TYPES, true) ? strtolower($move['type']) : ($types[0] ?? 'normal'),
                'category' => $move['category'] ?? 'special',
                'power' => isset($move['power']) && is_numeric($move['power']) ? (int) $move['power'] : '-',
                'accuracy' => isset($move['accuracy']) && is_numeric($move['accuracy']) ? (int) $move['accuracy'] : '-',
                'pp' => isset($move['pp']) && is_numeric($move['pp']) ? (int) $move['pp'] : '-',
                'effect' => $move['effect'] ?? 'Ataque criado para esta fusao.',
            ];
        }

        return array_slice($normalized, 0, 8);
    }

    private function sanitizeEvolutions($evolutions, string $selfName, int $selfNumber, ?string $image): array
    {
        if (! is_array($evolutions)) {
            return [[
                'id' => $selfNumber,
                'name' => $selfName,
                'image' => $image,
            ]];
        }

        $normalized = [];
        foreach ($evolutions as $evolution) {
            if (is_string($evolution)) {
                $evolution = ['name' => $evolution];
            }

            if (! is_array($evolution) || empty($evolution['name'])) {
                continue;
            }

            $name = (string) $evolution['name'];

            $normalized[] = [
                'id' => Str::lower($name) === Str::lower($selfName) ? $selfNumber : ($evolution['id'] ?? null),
                'name' => $name,
                'image' => $evolution['image'] ?? $image,
            ];
        }

        if (! $normalized) {
            $normalized[] = [
                'id' => $selfNumber,
                'name' => $selfName,
                'image' => $image,
            ];
        }

        $hasSelf = collect($normalized)->contains(fn ($evolution) => (int) ($evolution['id'] ?? 0) === $selfNumber);
        if (! $hasSelf) {
            $normalized[] = [
                'id' => $selfNumber,
                'name' => $selfName,
                'image' => $image,
            ];
        }

        return $normalized;
    }

    private function sanitizeVariants($variants, ?string $image): array
    {
        if (! is_array($variants)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($variant) use ($image) {
            if (is_string($variant)) {
                $variant = ['name' => $variant, 'form' => $variant];
            }

            if (! is_array($variant) || empty($variant['name'])) {
                return null;
            }

            return [
                'id' => $variant['id'] ?? null,
                'name' => $variant['name'],
                'form' => $variant['form'] ?? $variant['name'],
                'image' => $variant['image'] ?? $image,
                'is_current' => (bool) ($variant['is_current'] ?? false),
            ];
        }, $variants)));
    }

    private function rarityStats(string $rarity): array
    {
        $base = match ($rarity) {
            'uncommon' => 58,
            'rare' => 72,
            'epic' => 86,
            'legendary' => 105,
            'mythical' => 112,
            default => 45,
        };

        return [
            'hp' => $base + 6,
            'attack' => $base + 4,
            'defense' => $base,
            'special-attack' => $base + 8,
            'special-defense' => $base,
            'speed' => $base + 5,
        ];
    }

    private function defaultMovesForTypes(array $types): array
    {
        $type = $types[0] ?? 'normal';

        return [
            [
                'name' => 'Golpe '.$this->displayName($type),
                'type' => $type,
                'category' => 'special',
                'power' => 70,
                'accuracy' => 95,
                'pp' => 15,
                'effect' => 'Um ataque criado automaticamente para combinar com a tipagem principal.',
            ],
            [
                'name' => 'Investida Rara',
                'type' => 'normal',
                'category' => 'physical',
                'power' => 50,
                'accuracy' => 100,
                'pp' => 25,
                'effect' => 'Um ataque fisico confiavel para batalhas rapidas.',
            ],
        ];
    }

    private function storePokemonImage(Request $request, string $field = 'image'): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('pokemon-images', 'public');
    }

    private function publicImageUrl(?string $path): ?string
    {
        return $path ? '/storage/'.ltrim($path, '/') : null;
    }

    private function deleteStoredPokemonImages(array $pokemons): void
    {
        foreach ($pokemons as $pokemon) {
            if (! empty($pokemon['image_path'])) {
                Storage::disk('public')->delete($pokemon['image_path']);
            }
        }
    }

    private function storeGeneratedPokemonSvg(?string $svg, string $name, int $pokedexNumber, array $types): ?string
    {
        $svg = $this->sanitizeSvg($svg) ?: $this->fallbackPokemonSvg($name, $types);
        $path = 'pokemon-images/generated-'.$pokedexNumber.'-'.Str::slug($name ?: 'pokemon').'.svg';

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private function sanitizeSvg(?string $svg): ?string
    {
        if (! filled($svg)) {
            return null;
        }

        $svg = trim((string) $svg);

        if (preg_match('/<svg\b[\s\S]*<\/svg>/i', $svg, $matches)) {
            $svg = $matches[0];
        }

        if (! str_starts_with(strtolower(ltrim($svg)), '<svg')) {
            return null;
        }

        $svg = preg_replace('/<\?xml[\s\S]*?\?>/i', '', $svg);
        $svg = preg_replace('/<!doctype[\s\S]*?>/i', '', $svg);
        $svg = preg_replace('/<script\b[\s\S]*?<\/script>/i', '', $svg);
        $svg = preg_replace('/<foreignObject\b[\s\S]*?<\/foreignObject>/i', '', $svg);
        $svg = preg_replace('/<iframe\b[\s\S]*?<\/iframe>/i', '', $svg);
        $svg = preg_replace('/<object\b[\s\S]*?<\/object>/i', '', $svg);
        $svg = preg_replace('/<embed\b[\s\S]*?<\/embed>/i', '', $svg);
        $svg = preg_replace('/<image\b[^>]*>/i', '', $svg);
        $svg = preg_replace('/\son[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $svg);
        $svg = preg_replace('/(href|xlink:href)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $svg);
        $svg = preg_replace('/javascript\s*:/i', '', $svg);
        $svg = preg_replace('/data\s*:/i', '', $svg);

        if (! str_contains($svg, 'xmlns=')) {
            $svg = preg_replace('/<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $svg, 1);
        }

        if (! str_contains($svg, 'viewBox=')) {
            $svg = preg_replace('/<svg\b/i', '<svg viewBox="0 0 320 320"', $svg, 1);
        }

        return trim($svg);
    }

    private function fallbackPokemonSvg(string $name, array $types): string
    {
        $primary = $this->typeColor($types[0] ?? 'normal');
        $secondary = $this->typeColor($types[1] ?? ($types[0] ?? 'normal'), true);
        $safeName = htmlspecialchars($this->displayName($name), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320">
  <rect width="320" height="320" rx="36" fill="#0f172a"/>
  <circle cx="96" cy="88" r="58" fill="{$primary}" opacity="0.34"/>
  <circle cx="230" cy="94" r="68" fill="{$secondary}" opacity="0.3"/>
  <ellipse cx="160" cy="188" rx="82" ry="74" fill="{$primary}"/>
  <ellipse cx="126" cy="144" rx="38" ry="46" fill="{$secondary}"/>
  <ellipse cx="194" cy="144" rx="38" ry="46" fill="{$secondary}"/>
  <circle cx="132" cy="178" r="11" fill="#020617"/>
  <circle cx="188" cy="178" r="11" fill="#020617"/>
  <circle cx="136" cy="174" r="4" fill="#ffffff"/>
  <circle cx="192" cy="174" r="4" fill="#ffffff"/>
  <path d="M136 216 Q160 234 184 216" fill="none" stroke="#020617" stroke-width="8" stroke-linecap="round"/>
  <path d="M88 232 Q64 254 42 238" fill="none" stroke="{$secondary}" stroke-width="18" stroke-linecap="round"/>
  <path d="M232 232 Q256 254 278 238" fill="none" stroke="{$secondary}" stroke-width="18" stroke-linecap="round"/>
  <text x="160" y="292" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="700" fill="#f8fafc">{$safeName}</text>
</svg>
SVG;
    }

    private function typeColor(string $type, bool $alternate = false): string
    {
        $colors = [
            'normal' => ['#94a3b8', '#cbd5e1'],
            'fire' => ['#ef4444', '#f97316'],
            'water' => ['#2563eb', '#38bdf8'],
            'electric' => ['#facc15', '#fde047'],
            'grass' => ['#22c55e', '#84cc16'],
            'ice' => ['#67e8f9', '#bae6fd'],
            'fighting' => ['#b91c1c', '#f97316'],
            'poison' => ['#9333ea', '#c084fc'],
            'ground' => ['#ca8a04', '#fbbf24'],
            'flying' => ['#60a5fa', '#a78bfa'],
            'psychic' => ['#ec4899', '#f472b6'],
            'bug' => ['#65a30d', '#bef264'],
            'rock' => ['#78716c', '#a8a29e'],
            'ghost' => ['#6d28d9', '#8b5cf6'],
            'dragon' => ['#4f46e5', '#f97316'],
            'dark' => ['#1f2937', '#64748b'],
            'steel' => ['#64748b', '#cbd5e1'],
            'fairy' => ['#f0abfc', '#fb7185'],
        ];

        $pair = $colors[strtolower($type)] ?? $colors['normal'];

        return $alternate ? $pair[1] : $pair[0];
    }

    private function fallbackFusionMoves(array $first, array $second, string $type): array
    {
        $names = array_values(array_unique(array_filter(array_merge($first['moves'], $second['moves']))));
        $names = array_slice($names, 0, 6);

        if (! $names) {
            $names = ['Fusion Strike'];
        }

        return array_map(fn ($name) => [
            'name' => $this->displayName($name),
            'type' => $type,
            'category' => 'special',
            'power' => 70,
            'accuracy' => 95,
            'pp' => 15,
            'effect' => 'Um golpe herdado da fusao entre os dois Pokemon base.',
        ], $names);
    }

    private function blendNames(string $first, string $second): string
    {
        $a = preg_replace('/[^a-z]/i', '', $first) ?: 'Poke';
        $b = preg_replace('/[^a-z]/i', '', $second) ?: 'mon';

        return ucfirst(strtolower(substr($a, 0, max(2, (int) ceil(strlen($a) / 2))).substr($b, (int) floor(strlen($b) / 2))));
    }

    private function generationLabelForId(int $id): string
    {
        $key = $this->generationKeyForId($id);

        return self::GENERATIONS[$key]['label'] ?? 'Geracao desconhecida';
    }

    private function generationKeyForId(int $id): string
    {
        foreach (self::GENERATIONS as $key => $generation) {
            if ($key === 'custom') {
                continue;
            }

            if ($id >= $generation['from'] && $id <= $generation['to']) {
                return $key;
            }
        }

        return 'custom';
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

        $cacheKey = 'translate-'.md5($from.'-'.$to.'-'.$text);

        return Cache::store('file')->remember($cacheKey, now()->addDays(30), function () use ($text, $from, $to) {
            $response = Http::timeout(8)->get('https://translate.googleapis.com/translate_a/single', [
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
        });
    }

    private function extractEnglishText(array $data, string $entriesKey, string $textKey, string $langKey, string $langNameKey): string
    {
        if (empty($data[$entriesKey]) || ! is_array($data[$entriesKey])) {
            return 'Descricao nao disponivel.';
        }

        foreach ($data[$entriesKey] as $entry) {
            if (! empty($entry[$langKey][$langNameKey] ?? null) && $entry[$langKey][$langNameKey] === 'en') {
                return trim(str_replace(["\n", "\r", "\f"], ' ', $entry[$textKey]));
            }
        }

        return 'Descricao nao disponivel.';
    }

    private function displayName(string $name): string
    {
        return Str::of($name)->replace('-', ' ')->title()->toString();
    }
}
