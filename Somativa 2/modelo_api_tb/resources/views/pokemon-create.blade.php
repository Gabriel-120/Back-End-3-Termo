<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                radial-gradient(circle at 12% 8%, rgba(34, 197, 94, 0.2), transparent 24%),
                radial-gradient(circle at 88% 12%, rgba(59, 130, 246, 0.2), transparent 24%),
                linear-gradient(180deg, #0f172a 0%, #111827 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    @php
        $oldMoves = old('moves', [[
            'name' => '',
            'type' => 'normal',
            'category' => 'physical',
            'power' => 40,
            'accuracy' => 100,
            'pp' => 35,
            'effect' => '',
        ]]);
    @endphp

    <main class="mx-auto w-full max-w-[1180px]">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-[#22c55e]">Criacao manual</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Novo Pokemon</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg border border-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Voltar</a>
                <a href="{{ route('pokemon.list') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Lista</a>
            </div>
        </nav>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-[#fecaca] bg-[#fee2e2] p-4 text-sm font-semibold text-[#991b1b]">
                Revise os campos destacados antes de criar o Pokemon.
            </div>
        @endif

        <form method="POST" action="{{ route('pokemon.store.manual') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="grid gap-4 rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl md:grid-cols-2">
                <div class="md:col-span-2 rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4">
                    <label class="flex items-start gap-3">
                        <input id="use-ai" type="checkbox" name="use_ai" value="1" @checked(old('use_ai')) @disabled(! $groqConfigured)
                            class="mt-1 h-4 w-4 accent-[#22c55e]">
                        <span>
                            <span class="block text-sm font-black">Gerar detalhes com Groq</span>
                            <span class="mt-1 block text-sm font-medium text-[#475569]">
                                @if($groqConfigured)
                                    Com essa opcao voce preenche nome, tipagens e raridade; se nao enviar imagem, o sistema gera uma ilustracao SVG.
                                @else
                                    Configure GROQ_API_KEY no .env para ativar. Sem chave, use o preenchimento completo abaixo.
                                @endif
                            </span>
                        </span>
                    </label>
                </div>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Nome</span>
                    <input name="name" value="{{ old('name') }}" required
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                    @error('name') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Numero na Pokedex</span>
                    <input type="number" name="pokedex_number" value="{{ old('pokedex_number', $nextPokedexNumber) }}" min="10001"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                    @error('pokedex_number') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Geracao</span>
                    <select name="generation" required
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                        @foreach($generations as $key => $generation)
                            <option value="{{ $key }}" @selected(old('generation', 'custom') === (string) $key)>{{ $generation['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Raridade</span>
                    <select name="rarity" required
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                        @foreach($rarities as $key => $label)
                            <option value="{{ $key }}" @selected(old('rarity', 'common') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('rarity') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Imagem do Pokemon</span>
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] bg-white px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                    @error('image') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">URL da imagem</span>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                    @error('image_url') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <div class="md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Tipagens</span>
                    <div class="mt-2 grid gap-2 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach($types as $type)
                            <label class="flex items-center gap-2 rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-bold">
                                <input type="checkbox" name="types[]" value="{{ $type }}" @checked(in_array($type, old('types', []), true))
                                    class="h-4 w-4 accent-[#22c55e]">
                                {{ ucfirst($type) }}
                            </label>
                        @endforeach
                    </div>
                    @error('types') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </div>

                <label class="md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Adicionar em linha evolutiva existente</span>
                    <select name="existing_evolution_ref"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                        <option value="">Nao adicionar a uma linha existente</option>
                        @foreach(collect($evolutionOptions)->groupBy('group') as $group => $options)
                            <optgroup label="{{ $group }}">
                                @foreach($options as $option)
                                    <option value="{{ $option['value'] }}" @selected(old('existing_evolution_ref') === $option['value'])>{{ $option['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-[#64748b]">
                        Se escolher uma opcao, o novo Pokemon entra na mesma linha evolutiva do Pokemon selecionado.
                    </span>
                    @error('existing_evolution_ref') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label class="manual-only">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Altura</span>
                    <input type="number" step="0.1" name="height" value="{{ old('height', 10) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label class="manual-only">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Peso</span>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight', 10) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label class="manual-only">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Experiencia base</span>
                    <input type="number" name="base_experience" value="{{ old('base_experience', 64) }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label class="manual-only">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Taxa de captura</span>
                    <input type="number" name="capture_rate" value="{{ old('capture_rate', 45) }}" min="0" max="255"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label class="manual-only md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Descricao</span>
                    <textarea name="description" rows="4"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('description') }}</textarea>
                    @error('description') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>
            </section>

            <section class="manual-only rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
                <h2 class="text-lg font-black tracking-normal">Status</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($statFields as $key => $label)
                        <label>
                            <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">{{ $label }}</span>
                            <input type="number" name="stats[{{ $key }}]" value="{{ old('stats.'.$key, 45) }}" min="1" max="999"
                                class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="manual-only rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
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
                                    <option value="{{ $type }}" @selected(($move['type'] ?? 'normal') === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            <select name="moves[{{ $index }}][category]" class="rounded-lg border border-[#cbd5e1] px-3 py-2 text-sm font-semibold outline-none">
                                @foreach(['physical' => 'Fisico', 'special' => 'Especial', 'status' => 'Status'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($move['category'] ?? 'physical') === $value)>{{ $label }}</option>
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

            <section class="manual-only grid gap-4 rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl md:grid-cols-2">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Habilidades</span>
                    <textarea name="abilities" rows="4" placeholder="Uma por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('abilities') }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Locais</span>
                    <textarea name="locations" rows="4" placeholder="Um por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('locations') }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Linha evolutiva</span>
                    <textarea name="evolution_line" rows="4" placeholder="Nome ou ID|Nome|Imagem, um por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('evolution_line') }}</textarea>
                    <span class="mt-1 block text-xs font-bold text-[#64748b]">Este campo e usado quando nenhuma linha existente for selecionada.</span>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Variantes</span>
                    <textarea name="variants" rows="4" placeholder="Uma por linha"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('variants') }}</textarea>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Cor</span>
                    <input name="color" value="{{ old('color') }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Habitat</span>
                    <input name="habitat" value="{{ old('habitat') }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Formato</span>
                    <input name="shape" value="{{ old('shape') }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Crescimento</span>
                    <input name="growth_rate" value="{{ old('growth_rate') }}"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#22c55e]">
                </label>

                <div class="flex flex-wrap gap-3 md:col-span-2">
                    @foreach(['is_legendary' => 'Lendario', 'is_mythical' => 'Mitico', 'is_baby' => 'Bebe'] as $field => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-bold">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field))
                                class="h-4 w-4 accent-[#22c55e]">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <label class="md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Dados extras da API em JSON</span>
                    <textarea name="extra_json" rows="5" placeholder='{"campo_extra": "valor"}'
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 font-mono text-sm outline-none focus:ring-2 focus:ring-[#22c55e]">{{ old('extra_json') }}</textarea>
                    @error('extra_json') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg border border-white/20 px-6 py-3 text-sm font-bold uppercase tracking-[0.18em] text-white transition hover:bg-white/10">Cancelar</a>
                <button type="submit"
                    class="rounded-lg bg-[#22c55e] px-6 py-3 text-sm font-black uppercase tracking-[0.18em] text-[#052e16] transition hover:bg-[#86efac]">Criar Pokemon</button>
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

        const useAiToggle = document.getElementById('use-ai');
        const manualOnlyBlocks = document.querySelectorAll('.manual-only');

        function syncCreationMode() {
            const useAi = useAiToggle && useAiToggle.checked;
            manualOnlyBlocks.forEach(block => {
                block.classList.toggle('hidden', useAi);
            });
        }

        if (useAiToggle) {
            useAiToggle.addEventListener('change', syncCreationMode);
            syncCreationMode();
        }
    </script>
</body>

</html>
