<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pokemon Criados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                linear-gradient(135deg, rgba(239, 68, 68, 0.14) 0 18%, transparent 18% 100%),
                linear-gradient(225deg, rgba(250, 204, 21, 0.12) 0 20%, transparent 20% 100%),
                linear-gradient(180deg, #101827 0%, #0f172a 48%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    @php
        $sourceLabels = [
            'fusion' => ['label' => 'Fusion', 'class' => 'bg-[#7c3aed]'],
            'ai' => ['label' => 'IA', 'class' => 'bg-[#2563eb]'],
            'manual' => ['label' => 'Criado', 'class' => 'bg-[#ef4444]'],
        ];
    @endphp

    <main class="mx-auto w-full max-w-[1220px]">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-[#facc15]">Gerenciar criados</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Editar e excluir Pokemon</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokemon.list') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Lista</a>
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Criar/Fundir</a>
                <a href="{{ route('pokedex.show') }}"
                    class="rounded-lg border border-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Pokedex</a>
            </div>
        </nav>

        @if(session('status'))
            <div class="mb-5 rounded-lg border border-[#bbf7d0] bg-[#dcfce7] p-4 text-sm font-bold text-[#14532d]">
                {{ session('status') }}
            </div>
        @endif

        <section class="mb-6 grid gap-4 lg:grid-cols-[1fr_300px]">
            <div class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">Painel</p>
                        <h2 class="mt-1 text-2xl font-black tracking-normal">{{ count($pokemons) }} Pokemon personalizado(s)</h2>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-sm">
                        <div class="rounded-lg bg-[#f1f5f9] px-3 py-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#64748b]">Criados</p>
                            <p class="mt-1 text-lg font-black">{{ collect($pokemons)->where('raw_source', 'manual')->count() }}</p>
                        </div>
                        <div class="rounded-lg bg-[#f1f5f9] px-3 py-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#64748b]">IA</p>
                            <p class="mt-1 text-lg font-black">{{ collect($pokemons)->where('raw_source', 'ai')->count() }}</p>
                        </div>
                        <div class="rounded-lg bg-[#f1f5f9] px-3 py-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#64748b]">Fusion</p>
                            <p class="mt-1 text-lg font-black">{{ collect($pokemons)->where('raw_source', 'fusion')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-[1fr_auto]">
                    <label>
                        <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Buscar</span>
                        <input id="manage-search" type="text" placeholder="Nome, numero, tipo ou origem"
                            class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#facc15]">
                    </label>

                    <form method="POST" action="{{ route('pokemon.delete.reset-samples') }}"
                        class="flex items-end"
                        onsubmit="return confirm('Isso vai apagar todos os Pokemon criados/fundidos e recriar os exemplos. Continuar?')">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-lg bg-[#facc15] px-4 py-3 text-sm font-black uppercase tracking-[0.14em] text-[#0f172a] transition hover:bg-[#fde68a]">
                            Resetar exemplos
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/10 p-5 shadow-2xl">
                <form method="POST" action="{{ route('pokemon.delete.bulk') }}" id="bulk-delete-form" onsubmit="return confirmBulkDelete(event)">
                    @csrf
                    @method('DELETE')
                </form>

                <label class="flex items-center gap-2 text-sm font-bold text-white">
                    <input type="checkbox" id="select-all" class="h-4 w-4 accent-[#ef4444]">
                    Selecionar todos
                </label>

                <div class="mt-4 grid gap-2">
                    <button type="submit" form="bulk-delete-form"
                        class="rounded-lg bg-[#ef4444] px-4 py-3 text-sm font-black uppercase tracking-[0.14em] text-white transition hover:bg-[#dc2626]">
                        Excluir selecionados
                    </button>
                    <button type="submit" form="bulk-delete-form" name="delete_all" value="1"
                        class="rounded-lg border border-[#fecaca] px-4 py-3 text-sm font-black uppercase tracking-[0.14em] text-[#fecaca] transition hover:bg-[#7f1d1d]">
                        Excluir tudo
                    </button>
                </div>
            </div>
        </section>

        <section id="pokemon-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($pokemons as $pokemon)
                @php
                    $sourceStyle = $sourceLabels[$pokemon['raw_source']] ?? $sourceLabels['manual'];
                    $searchText = strtolower($pokemon['name'].' '.$pokemon['pokedex_number'].' '.implode(' ', $pokemon['types']).' '.$pokemon['raw_source']);
                @endphp
                <article class="pokemon-card rounded-lg border border-white/10 bg-white p-4 text-[#0f172a] shadow-xl transition hover:-translate-y-1 hover:border-[#facc15] hover:shadow-2xl"
                    data-search="{{ $searchText }}">
                    <div class="flex items-start justify-between gap-3">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" form="bulk-delete-form" name="ids[]" value="{{ $pokemon['pokedex_number'] }}"
                                class="pokemon-check mt-2 h-4 w-4 shrink-0 accent-[#ef4444]">
                            <span>
                                <span class="block text-xs font-black uppercase tracking-[0.2em] text-[#64748b]">#{{ $pokemon['pokedex_number'] }}</span>
                                <span class="mt-1 block text-xl font-black tracking-normal">{{ $pokemon['name'] }}</span>
                            </span>
                        </label>
                        <span class="rounded-lg {{ $sourceStyle['class'] }} px-2 py-1 text-[10px] font-black uppercase tracking-[0.14em] text-white">
                            {{ $sourceStyle['label'] }}
                        </span>
                    </div>

                    <div class="mt-4 h-40 rounded-lg bg-[#e2e8f0] p-3">
                        <img src="{{ $pokemon['image'] }}" alt="{{ $pokemon['name'] }}"
                            class="mx-auto h-full w-full object-contain">
                    </div>

                    <div class="mt-3 flex min-h-7 flex-wrap gap-2">
                        @forelse($pokemon['types'] as $type)
                            <span class="rounded-lg bg-[#111827] px-2 py-1 text-[10px] font-black uppercase text-white">{{ ucfirst($type) }}</span>
                        @empty
                            <span class="rounded-lg bg-[#64748b] px-2 py-1 text-[10px] font-black uppercase text-white">Sem tipo</span>
                        @endforelse
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <a href="{{ route('pokedex.show', ['id' => $pokemon['pokedex_number']]) }}"
                            class="rounded-lg bg-[#e2e8f0] px-3 py-2 text-center text-xs font-black uppercase text-[#0f172a] transition hover:bg-[#cbd5e1]">
                            Ver
                        </a>
                        <a href="{{ route('pokemon.delete.edit', ['id' => $pokemon['pokedex_number']]) }}"
                            class="rounded-lg bg-[#facc15] px-3 py-2 text-center text-xs font-black uppercase text-[#0f172a] transition hover:bg-[#fde68a]">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('pokemon.delete.destroy', ['id' => $pokemon['pokedex_number']]) }}"
                            data-pokemon-name="{{ $pokemon['name'] }}" onsubmit="return confirmSingleDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full rounded-lg bg-[#ef4444] px-3 py-2 text-xs font-black uppercase text-white transition hover:bg-[#dc2626]">
                                Excluir
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-white/10 bg-white/10 p-8 text-center text-[#cbd5e1] sm:col-span-2 lg:col-span-3 xl:col-span-4">
                    Nenhum Pokemon criado ou fundido cadastrado.
                </div>
            @endforelse
        </section>
    </main>

    <script>
        const selectAll = document.getElementById('select-all');
        const checks = Array.from(document.querySelectorAll('.pokemon-check'));
        const searchInput = document.getElementById('manage-search');
        const cards = Array.from(document.querySelectorAll('.pokemon-card'));

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                checks.forEach(check => {
                    if (!check.closest('.pokemon-card').classList.contains('hidden')) {
                        check.checked = selectAll.checked;
                    }
                });
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim().toLowerCase();
                cards.forEach(card => {
                    card.classList.toggle('hidden', query !== '' && !card.dataset.search.includes(query));
                });

                if (selectAll) {
                    selectAll.checked = false;
                }
            });
        }

        function confirmBulkDelete(event) {
            const submitter = event.submitter;
            if (submitter && submitter.name === 'delete_all') {
                return confirm('Tem certeza que deseja excluir todos os Pokemon criados/fundidos?');
            }

            const selected = checks.filter(check => check.checked).length;
            if (!selected) {
                alert('Selecione pelo menos um Pokemon para excluir.');
                return false;
            }

            return confirm(`Excluir ${selected} Pokemon selecionado(s)?`);
        }

        function confirmSingleDelete(form) {
            return confirm(`Excluir ${form.dataset.pokemonName}?`);
        }
    </script>
</body>

</html>
