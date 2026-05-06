<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                linear-gradient(135deg, rgba(250, 204, 21, 0.14) 0 18%, transparent 18% 100%),
                linear-gradient(225deg, rgba(239, 68, 68, 0.12) 0 20%, transparent 20% 100%),
                linear-gradient(180deg, #101827 0%, #0f172a 50%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    @php
        $typeColors = [
            'normal' => ['bg' => '#A8A77A', 'text' => '#111827', 'border' => '#8b8a62'],
            'fire' => ['bg' => '#EE8130', 'text' => '#ffffff', 'border' => '#c7661f'],
            'water' => ['bg' => '#6390F0', 'text' => '#ffffff', 'border' => '#426fd0'],
            'electric' => ['bg' => '#F7D02C', 'text' => '#111827', 'border' => '#d0ad13'],
            'grass' => ['bg' => '#7AC74C', 'text' => '#102a12', 'border' => '#5ea733'],
            'ice' => ['bg' => '#96D9D6', 'text' => '#0f172a', 'border' => '#6bbfbc'],
            'fighting' => ['bg' => '#C22E28', 'text' => '#ffffff', 'border' => '#9d211e'],
            'poison' => ['bg' => '#A33EA1', 'text' => '#ffffff', 'border' => '#842982'],
            'ground' => ['bg' => '#E2BF65', 'text' => '#111827', 'border' => '#c5a34a'],
            'flying' => ['bg' => '#A98FF3', 'text' => '#111827', 'border' => '#846dd3'],
            'psychic' => ['bg' => '#F95587', 'text' => '#ffffff', 'border' => '#d93668'],
            'bug' => ['bg' => '#A6B91A', 'text' => '#111827', 'border' => '#859512'],
            'rock' => ['bg' => '#B6A136', 'text' => '#111827', 'border' => '#927f22'],
            'ghost' => ['bg' => '#735797', 'text' => '#ffffff', 'border' => '#594179'],
            'dragon' => ['bg' => '#6F35FC', 'text' => '#ffffff', 'border' => '#5423d4'],
            'dark' => ['bg' => '#705746', 'text' => '#ffffff', 'border' => '#503b2e'],
            'steel' => ['bg' => '#B7B7CE', 'text' => '#111827', 'border' => '#9292ad'],
            'fairy' => ['bg' => '#D685AD', 'text' => '#111827', 'border' => '#b96790'],
        ];

        $sourceLabels = [
            'fusion' => ['label' => 'Fusion', 'class' => 'bg-[#7c3aed]'],
            'ai' => ['label' => 'IA', 'class' => 'bg-[#2563eb]'],
            'manual' => ['label' => 'Criado', 'class' => 'bg-[#ef4444]'],
            'api' => ['label' => 'API', 'class' => 'bg-[#111827]'],
        ];
    @endphp

    <main class="mx-auto w-full max-w-[1280px]">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.28em] text-[#facc15]">Pokedex Nacional</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Lista de Pokemon</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokedex.show') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Pokedex</a>
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg bg-[#ef4444] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Criar/Fundir</a>
                <a href="{{ route('pokemon.delete.index') }}"
                    class="rounded-lg bg-[#facc15] px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fde68a]">Gerenciar</a>
                <a href="{{ route('pokemon.game') }}"
                    class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Jogos</a>
            </div>
        </nav>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-[#bbf7d0] bg-[#dcfce7] p-4 text-sm font-bold text-[#14532d]">
                {{ session('success') }}
            </div>
        @endif

        <section class="mb-6 grid gap-4 lg:grid-cols-[1fr_280px]">
            <form method="GET" action="{{ route('pokemon.list') }}"
                class="grid gap-3 rounded-lg border border-white/10 bg-white p-4 text-[#0f172a] shadow-2xl md:grid-cols-2 xl:grid-cols-6">
                <label class="xl:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Nome ou ID</span>
                    <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Ex: pikachu ou 25"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Geracao</span>
                    <select name="generation"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        <option value="">Todas</option>
                        @foreach($generations as $key => $generation)
                            <option value="{{ $key }}" @selected($filters['generation'] === (string) $key)>{{ $generation['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Tipo</span>
                    <select name="type"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        <option value="">Todos</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Origem</span>
                    <select name="origin"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        <option value="all" @selected($filters['origin'] === 'all')>Todos</option>
                        <option value="api" @selected($filters['origin'] === 'api')>PokeAPI</option>
                        <option value="custom" @selected($filters['origin'] === 'custom')>Criados</option>
                        <option value="manual" @selected($filters['origin'] === 'manual')>Manuais</option>
                        <option value="ai" @selected($filters['origin'] === 'ai')>Gerados IA</option>
                        <option value="fusion" @selected($filters['origin'] === 'fusion')>Fusions</option>
                    </select>
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Ordem</span>
                    <select name="sort"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                        <option value="number_asc" @selected($filters['sort'] === 'number_asc')>Numero crescente</option>
                        <option value="number_desc" @selected($filters['sort'] === 'number_desc')>Numero decrescente</option>
                        <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Nome A-Z</option>
                        <option value="name_desc" @selected($filters['sort'] === 'name_desc')>Nome Z-A</option>
                    </select>
                </label>

                <div class="flex flex-wrap items-end gap-2 xl:col-span-6">
                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                    <button type="submit"
                        class="rounded-lg bg-[#facc15] px-6 py-3 text-sm font-black uppercase tracking-[0.18em] text-[#0f172a] transition hover:bg-[#fde68a]">Filtrar</button>
                    <a href="{{ route('pokemon.list') }}"
                        class="rounded-lg border border-[#cbd5e1] px-6 py-3 text-sm font-bold uppercase tracking-[0.18em] text-[#0f172a] transition hover:bg-[#f8fafc]">Limpar</a>
                </div>
            </form>

            <aside class="rounded-lg border border-white/10 bg-white/10 p-5 shadow-2xl">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-[#facc15]">Resultados</p>
                <p class="mt-2 text-4xl font-black">{{ $pokemons->total() }}</p>
                <div class="mt-5 grid grid-cols-2 gap-2 text-sm">
                    <div class="rounded-lg bg-white/10 p-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#cbd5e1]">Pagina</p>
                        <p class="mt-1 font-black">{{ $pokemons->currentPage() }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#cbd5e1]">Total</p>
                        <p class="mt-1 font-black">{{ max(1, $pokemons->lastPage()) }}</p>
                    </div>
                </div>
            </aside>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($pokemons as $card)
                @php
                    $sourceStyle = $card['is_custom']
                        ? ($sourceLabels[$card['source']] ?? $sourceLabels['manual'])
                        : $sourceLabels['api'];
                @endphp

                <article class="group overflow-hidden rounded-lg border border-white/10 bg-white text-[#0f172a] shadow-xl transition hover:-translate-y-1 hover:border-[#facc15] hover:shadow-2xl">
                    <a href="{{ $card['detail_url'] }}" class="block">
                        <div class="flex items-start justify-between gap-3 p-4 pb-0">
                            <div class="min-w-0">
                                <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">#{{ str_pad($card['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                <h2 class="mt-1 truncate text-xl font-black tracking-normal">{{ $card['name'] }}</h2>
                            </div>
                            <span class="rounded-lg {{ $sourceStyle['class'] }} px-2 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-white">
                                {{ $sourceStyle['label'] }}
                            </span>
                        </div>

                        <div class="mx-4 mt-4 h-44 rounded-lg bg-[#e2e8f0] p-3">
                            <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}"
                                class="mx-auto h-full w-full object-contain transition group-hover:scale-105">
                        </div>
                    </a>

                    <div class="p-4">
                        <div class="flex min-h-8 flex-wrap gap-2">
                            @forelse($card['types'] as $type)
                                @php
                                    $typeKey = strtolower($type);
                                    $typeStyle = $typeColors[$typeKey] ?? ['bg' => '#111827', 'text' => '#ffffff', 'border' => '#020617'];
                                @endphp
                                <span class="rounded-lg border px-3 py-1 text-xs font-black uppercase shadow-sm"
                                    style="background-color: {{ $typeStyle['bg'] }}; color: {{ $typeStyle['text'] }}; border-color: {{ $typeStyle['border'] }};">
                                    {{ ucfirst($type) }}
                                </span>
                            @empty
                                <span class="rounded-lg bg-[#475569] px-3 py-1 text-xs font-bold uppercase text-white">Sem tipo</span>
                            @endforelse
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-[#f1f5f9] p-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Geracao</p>
                                <p class="mt-1 truncate font-bold">{{ $card['generation'] }}</p>
                            </div>
                            <div class="rounded-lg bg-[#f1f5f9] p-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Evolucoes</p>
                                <p class="mt-1 font-bold">{{ $card['evolution_count'] }}</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-{{ $card['is_custom'] ? '2' : '1' }} gap-2">
                            <a href="{{ $card['detail_url'] }}"
                                class="rounded-lg bg-[#111827] px-3 py-2 text-center text-xs font-black uppercase text-white transition hover:bg-[#020617]">
                                Ver detalhes
                            </a>
                            @if($card['is_custom'])
                                <a href="{{ route('pokemon.delete.edit', ['id' => $card['id']]) }}"
                                    class="rounded-lg bg-[#facc15] px-3 py-2 text-center text-xs font-black uppercase text-[#0f172a] transition hover:bg-[#fde68a]">
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/10 p-8 text-center text-[#cbd5e1] sm:col-span-2 lg:col-span-3 xl:col-span-4">
                    Nenhum Pokemon encontrado.
                </div>
            @endforelse
        </section>

        <div class="mt-8 rounded-lg bg-white p-3 text-[#0f172a]">
            {{ $pokemons->links() }}
        </div>
    </main>
</body>

</html>
