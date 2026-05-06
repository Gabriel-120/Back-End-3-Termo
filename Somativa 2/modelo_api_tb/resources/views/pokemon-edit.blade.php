<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                linear-gradient(135deg, rgba(250, 204, 21, 0.12) 0 22%, transparent 22% 100%),
                linear-gradient(225deg, rgba(37, 99, 235, 0.12) 0 22%, transparent 22% 100%),
                linear-gradient(180deg, #101827 0%, #0f172a 48%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    @php
        $meta = $pokemon['meta'] ?? [];
        $imagePreview = ! empty($pokemon['image_path'])
            ? '/storage/'.ltrim($pokemon['image_path'], '/')
            : ($pokemon['image_url'] ?? 'https://placehold.co/320x320/0f172a/facc15?text=?');

        $stats = old('stats', [
            'hp' => data_get($pokemon, 'stats.hp', 45),
            'attack' => data_get($pokemon, 'stats.attack', 45),
            'defense' => data_get($pokemon, 'stats.defense', 45),
            'special_attack' => data_get($pokemon, 'stats.special-attack', data_get($pokemon, 'stats.special_attack', 45)),
            'special_defense' => data_get($pokemon, 'stats.special-defense', data_get($pokemon, 'stats.special_defense', 45)),
            'speed' => data_get($pokemon, 'stats.speed', 45),
        ]);

        $oldMoves = old('moves', ! empty($pokemon['moves']) ? $pokemon['moves'] : [[
            'name' => '',
            'type' => ($pokemon['types'][0] ?? 'normal'),
            'category' => 'physical',
            'power' => 40,
            'accuracy' => 100,
            'pp' => 35,
            'effect' => '',
        ]]);

        $abilitiesText = old('abilities', implode("\n", $pokemon['abilities'] ?? []));
        $locationsText = old('locations', implode("\n", $pokemon['locations'] ?? []));
        $evolutionsText = old('evolution_line', collect($pokemon['evolutions'] ?? [])->map(function ($row) {
            if (is_string($row)) {
                return $row;
            }

            $id = $row['id'] ?? null;
            $name = $row['name'] ?? '';
            $image = $row['image'] ?? '';
            $line = $id ? $id.'|'.$name : $name;

            return $image ? $line.'|'.$image : $line;
        })->filter()->implode("\n"));
        $variantsText = old('variants', collect($pokemon['variants'] ?? [])->map(function ($row) {
            return is_string($row) ? $row : ($row['form'] ?? $row['name'] ?? null);
        })->filter()->implode("\n"));
        $extraJson = old('extra_json', ! empty($meta['extra']) ? json_encode($meta['extra'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '');
        $selectedTypes = old('types', $pokemon['types'] ?? []);
    @endphp

    <main class="mx-auto w-full max-w-[1180px]">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-[#facc15]">Gerenciar criados</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Editar {{ $pokemon['name'] }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokemon.delete.index') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Gerenciar</a>
                <a href="{{ route('pokedex.show', ['id' => $pokemon['pokedex_number']]) }}"
                    class="rounded-lg border border-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Ver Pokemon</a>
                <a href="{{ route('pokemon.list', ['origin' => 'custom']) }}"
                    class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Lista</a>
            </div>
        </nav>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-[#fecaca] bg-[#fee2e2] p-4 text-sm font-semibold text-[#991b1b]">
                Revise os campos destacados antes de salvar as alteracoes.
            </div>
        @endif

        <form method="POST" action="{{ route('pokemon.delete.update', ['id' => $pokemon['pokedex_number']]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="pokedex_number" value="{{ $pokemon['pokedex_number'] }}">

            <section class="grid gap-4 rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl lg:grid-cols-[280px_1fr]">
                <aside class="rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Previa</p>
                    <div class="mt-3 h-56 rounded-lg bg-[#0f172a] p-4">
                        <img src="{{ $imagePreview }}" alt="{{ $pokemon['name'] }}" class="mx-auto h-full w-full object-contain">
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg bg-white p-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Numero</p>
                            <p class="mt-1 font-black">#{{ $pokemon['pokedex_number'] }}</p>
                        </div>
                        <div class="rounded-lg bg-white p-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Origem</p>
                            <p class="mt-1 font-black uppercase">{{ $pokemon['source'] ?? 'manual' }}</p>
                        </div>
                    </div>
                </aside>

                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Nome</span>
                        <input name="name" value="{{ old('name', $pokemon['name']) }}" required
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        @error('name') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Geracao</span>
                        <select name="generation" required
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                            @foreach($generations as $key => $generation)
                                <option value="{{ $key }}" @selected(old('generation', $pokemon['generation'] ?? 'custom') === (string) $key)>{{ $generation['label'] }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Raridade</span>
                        <select name="rarity" required
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                            @foreach($rarities as $key => $label)
                                <option value="{{ $key }}" @selected(old('rarity', $pokemon['rarity'] ?? data_get($meta, 'rarity', 'common')) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('rarity') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">URL da imagem</span>
                        <input type="url" name="image_url" value="{{ old('image_url', $pokemon['image_url'] ?? '') }}" placeholder="https://..."
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        @error('image_url') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Trocar imagem enviada</span>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] bg-white px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        @error('image') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                    </label>

                    <label class="flex items-center gap-3 rounded-lg border border-[#cbd5e1] bg-[#f8fafc] px-4 py-3 text-sm font-bold">
                        <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))
                            class="h-4 w-4 accent-[#ef4444]">
                        Remover imagem enviada atual
                    </label>

                    <div class="md:col-span-2">
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Tipagens</span>
                        <div class="mt-2 grid gap-2 sm:grid-cols-3 lg:grid-cols-6">
                            @foreach($types as $type)
                                <label class="flex items-center gap-2 rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-bold">
                                    <input type="checkbox" name="types[]" value="{{ $type }}" @checked(in_array($type, $selectedTypes, true))
                                        class="h-4 w-4 accent-[#facc15]">
                                    {{ ucfirst($type) }}
                                </label>
                            @endforeach
                        </div>
                        @error('types') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>

                    <label class="md:col-span-2">
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Adicionar em linha evolutiva existente</span>
                        <select name="existing_evolution_ref"
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                            <option value="">Manter linha digitada abaixo</option>
                            @foreach(collect($evolutionOptions)->groupBy('group') as $group => $options)
                                <optgroup label="{{ $group }}">
                                    @foreach($options as $option)
                                        <option value="{{ $option['value'] }}" @selected(old('existing_evolution_ref') === $option['value'])>{{ $option['label'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </label>
                </div>
            </section>

            <section class="grid gap-4 rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl md:grid-cols-2 lg:grid-cols-4">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Altura</span>
                    <input type="number" step="0.1" name="height" value="{{ old('height', $pokemon['height'] ?? 10) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Peso</span>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight', $pokemon['weight'] ?? 10) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Experiencia base</span>
                    <input type="number" name="base_experience" value="{{ old('base_experience', $pokemon['base_experience'] ?? 64) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Taxa de captura</span>
                    <input type="number" name="capture_rate" value="{{ old('capture_rate', data_get($meta, 'capture_rate', 45)) }}" min="0" max="255"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>
                <label class="md:col-span-2 lg:col-span-4">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Descricao</span>
                    <textarea name="description" rows="4"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">{{ old('description', $pokemon['description'] ?? '') }}</textarea>
                    @error('description') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>
            </section>

            <section class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
                <h2 class="text-lg font-black tracking-normal">Status</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($statFields as $key => $label)
                        @php $fieldName = str_replace('-', '_', $key); @endphp
                        <label>
                            <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">{{ $label }}</span>
                            <input type="number" name="stats[{{ $fieldName }}]" value="{{ $stats[$fieldName] ?? $stats[$key] ?? 45 }}" min="1" max="999"
                                class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-black tracking-normal">Ataques</h2>
                    <button type="button" onclick="addMoveRow()"
                        class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Adicionar ataque</button>
                </div>

                <div id="moves-wrapper" class="mt-4 space-y-4">
                    @foreach($oldMoves as $index => $move)
                        <div class="move-row grid gap-3 rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4 md:grid-cols-6">
                            <input name="moves[{{ $index }}][name]" value="{{ $move['name'] ?? '' }}" placeholder="Nome"
                                class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none md:col-span-2">
                            <select name="moves[{{ $index }}][type]" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected(strtolower($move['type'] ?? 'normal') === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            <select name="moves[{{ $index }}][category]" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                                @foreach(['physical' => 'Fisico', 'special' => 'Especial', 'status' => 'Status'] as $value => $label)
                                    <option value="{{ $value }}" @selected(strtolower($move['category'] ?? 'physical') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="moves[{{ $index }}][power]" value="{{ $move['power'] ?? 40 }}" placeholder="Poder"
                                class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                            <input type="number" name="moves[{{ $index }}][accuracy]" value="{{ $move['accuracy'] ?? 100 }}" placeholder="Precisao"
                                class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                            <input type="number" name="moves[{{ $index }}][pp]" value="{{ $move['pp'] ?? 35 }}" placeholder="PP"
                                class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                            <textarea name="moves[{{ $index }}][effect]" rows="2" placeholder="Efeito"
                                class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none md:col-span-5">{{ $move['effect'] ?? '' }}</textarea>
                            <button type="button" onclick="removeMoveRow(this)"
                                class="rounded-lg bg-[#ef4444] px-3 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Remover</button>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-4 rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl md:grid-cols-2">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Habilidades</span>
                    <textarea name="abilities" rows="4" placeholder="Uma por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">{{ $abilitiesText }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Locais</span>
                    <textarea name="locations" rows="4" placeholder="Um por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">{{ $locationsText }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Linha evolutiva</span>
                    <textarea name="evolution_line" rows="4" placeholder="Nome ou ID|Nome|Imagem, um por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">{{ $evolutionsText }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Variantes</span>
                    <textarea name="variants" rows="4" placeholder="Uma por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">{{ $variantsText }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Cor</span>
                    <input name="color" value="{{ old('color', data_get($meta, 'color')) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Habitat</span>
                    <input name="habitat" value="{{ old('habitat', data_get($meta, 'habitat')) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Formato</span>
                    <input name="shape" value="{{ old('shape', data_get($meta, 'shape')) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Crescimento</span>
                    <input name="growth_rate" value="{{ old('growth_rate', data_get($meta, 'growth_rate')) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>

                <div class="flex flex-wrap gap-3 md:col-span-2">
                    @foreach(['is_legendary' => 'Lendario', 'is_mythical' => 'Mitico', 'is_baby' => 'Bebe'] as $field => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-bold">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked((bool) old($field, data_get($meta, $field)))
                                class="h-4 w-4 accent-[#facc15]">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <label class="md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Dados extras em JSON</span>
                    <textarea name="extra_json" rows="5" placeholder='{"campo_extra": "valor"}'
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 font-mono text-sm outline-none focus:ring-2 focus:ring-[#facc15]">{{ $extraJson }}</textarea>
                    @error('extra_json') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('pokemon.delete.index') }}"
                    class="rounded-lg border border-white/20 px-6 py-3 text-sm font-bold uppercase tracking-[0.18em] text-white transition hover:bg-white/10">Cancelar</a>
                <button type="submit"
                    class="rounded-lg bg-[#facc15] px-6 py-3 text-sm font-black uppercase tracking-[0.18em] text-[#0f172a] transition hover:bg-[#fde68a]">Salvar alteracoes</button>
            </div>
        </form>
    </main>

    <script>
        const types = @json($types);
        let moveIndex = {{ count($oldMoves) }};

        function typeSelect(name) {
            return `<select name="${name}" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                ${types.map(type => `<option value="${type}">${type.charAt(0).toUpperCase() + type.slice(1)}</option>`).join('')}
            </select>`;
        }

        function addMoveRow() {
            const wrapper = document.getElementById('moves-wrapper');
            const row = document.createElement('div');
            row.className = 'move-row grid gap-3 rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4 md:grid-cols-6';
            row.innerHTML = `
                <input name="moves[${moveIndex}][name]" placeholder="Nome" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none md:col-span-2">
                ${typeSelect(`moves[${moveIndex}][type]`)}
                <select name="moves[${moveIndex}][category]" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                    <option value="physical">Fisico</option>
                    <option value="special">Especial</option>
                    <option value="status">Status</option>
                </select>
                <input type="number" name="moves[${moveIndex}][power]" value="40" placeholder="Poder" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                <input type="number" name="moves[${moveIndex}][accuracy]" value="100" placeholder="Precisao" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                <input type="number" name="moves[${moveIndex}][pp]" value="35" placeholder="PP" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                <textarea name="moves[${moveIndex}][effect]" rows="2" placeholder="Efeito" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none md:col-span-5"></textarea>
                <button type="button" onclick="removeMoveRow(this)" class="rounded-lg bg-[#ef4444] px-3 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Remover</button>
            `;
            wrapper.appendChild(row);
            moveIndex += 1;
        }

        function removeMoveRow(button) {
            const rows = document.querySelectorAll('.move-row');
            if (rows.length <= 1) {
                return;
            }
            button.closest('.move-row').remove();
        }
    </script>
</body>

</html>
