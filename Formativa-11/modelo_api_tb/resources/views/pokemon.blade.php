<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex API - Aula Prática</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pokedex-bg {
            background: radial-gradient(circle at top left, #ffcb05 0%, transparent 20%),
                radial-gradient(circle at top right, #ef4444 0%, transparent 20%),
                linear-gradient(180deg, #111827 0%, #0f172a 100%);
        }

        .pokedex-card {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            border: 10px solid #cbd5e1;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.35);
        }

        .pokedex-screen {
            background: radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0) 45%),
                linear-gradient(180deg, #0f172a 0%, #111827 100%);
        }

        .type-pill {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body class="pokedex-bg min-h-screen flex flex-col items-center justify-center px-4 py-8">
    <div class="w-full max-w-[1200px] mb-6">
        <div class="relative">
            <input type="text" id="search-input" placeholder="Pesquise por nome ou ID..." 
                class="w-full rounded-full bg-white px-6 py-3 text-sm font-medium text-[#0f172a] placeholder-[#94a3b8] shadow-lg focus:outline-none focus:ring-2 focus:ring-[#facc15]" />
            <div id="search-results" class="absolute top-full left-0 right-0 mt-2 rounded-[1.75rem] bg-white shadow-lg border border-[#e2e8f0] max-h-80 overflow-y-auto hidden z-50">
            </div>
        </div>
    </div>

    <div class="pokedex-card rounded-[2.5rem] max-w-[1200px] w-full overflow-hidden">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-0">
            <div class="lg:w-[360px] bg-[#f8fafc] p-6 lg:p-8 border-r border-[#cbd5e1] relative lg:sticky lg:top-8 self-start">
                <div class="relative z-10">
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-[0.32em] text-[#475569]">Pokedex Nacional</p>
                        <div class="mt-3 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.45em] text-[#475569]">No.</p>
                                <p class="text-2xl font-extrabold text-[#0f172a]">{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <button type="button" onclick="goBack()"
                                class="rounded-full bg-[#2563eb] px-3 py-2 text-[10px] uppercase tracking-[0.32em] text-white shadow-sm transition hover:bg-[#1d4ed8] {{ $showBackButton ? '' : 'hidden' }}">Voltar</button>
                        </div>
                    </div>

                    <div class="relative rounded-[2rem] bg-[#0f172a] p-4 shadow-inner pokedex-screen">
                        <img src="{{ $pokemon['sprites']['other']['official-artwork']['front_default'] }}"
                            alt="{{ ucfirst($pokemon['name']) }}" class="mx-auto h-56 w-auto object-contain" />
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[1.75rem] bg-white p-4 shadow-sm border border-[#cbd5e1]">
                            <h2 class="text-sm uppercase tracking-[0.28em] text-[#475569]">Nome</h2>
                            <p class="mt-2 text-3xl font-bold text-[#0f172a]">{{ ucfirst($pokemon['name']) }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Altura</p>
                                <p class="mt-2 text-sm font-semibold text-[#0f172a]">{{ $pokemon['height'] / 10 }} m</p>
                            </div>
                            <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Peso</p>
                                <p class="mt-2 text-sm font-semibold text-[#0f172a]">{{ $pokemon['weight'] / 10 }} kg</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Tipagem</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($pokemon['types'] as $tipo)
                                    <span class="type-pill inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase text-white bg-gradient-to-r from-[#2563eb] to-[#7c3aed]">{{ ucfirst($tipo['type']['name']) }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#111827] p-4 text-white">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Descrição</p>
                            <p class="mt-3 text-sm leading-6">{{ $description }}</p>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Linha de evolução</p>
                            <div class="mt-3 grid gap-3">
                                @if(count($evolutions))
                                    @foreach($evolutions as $evo)
                                        <button type="button" onclick="selectEvolution({{ $evo['id'] }})"
                                            class="flex items-center gap-3 rounded-3xl border px-3 py-3 transition text-left w-full {{ $evo['id'] === $pokemon['id'] ? 'border-[#f97316] bg-[#fee2b3]' : 'border-transparent bg-white/90 hover:bg-white' }}">
                                            <img src="{{ $evo['image'] ?? 'https://via.placeholder.com/80?text=?' }}"
                                                alt="{{ $evo['name'] }}" class="h-14 w-14 rounded-full object-contain bg-white p-1" />
                                            <div>
                                                <p class="text-[10px] uppercase tracking-[0.28em] text-[#475569]">#{{ str_pad($evo['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                                <p class="text-sm font-semibold text-[#0f172a]">{{ $evo['name'] }}</p>
                                                @if($evo['id'] === $pokemon['id'])
                                                    <span class="text-xs uppercase tracking-[0.3em] text-[#b45309]">Você está aqui</span>
                                                @endif
                                            </div>
                                        </button>
                                    @endforeach
                                @else
                                    <p class="text-sm font-semibold text-[#0f172a]">Sem evolução disponível</p>
                                @endif
                            </div>
                        </div>

                        @if(count($variants) > 1)
                            <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Variantes Regionais</p>
                                <div class="mt-3 grid gap-3">
                                    @foreach($variants as $variant)
                                        <button type="button" onclick="selectVariant({{ $variant['id'] }})"
                                            class="flex items-center gap-3 rounded-3xl border px-3 py-3 transition text-left w-full {{ $variant['is_current'] ? 'border-[#8b5cf6] bg-[#ede9fe]' : 'border-transparent bg-white/90 hover:bg-white' }}">
                                            <img src="{{ $variant['image'] ?? 'https://via.placeholder.com/80?text=?' }}"
                                                alt="{{ $variant['name'] }}" class="h-14 w-14 rounded-full object-contain bg-white p-1" />
                                            <div>
                                                <p class="text-[10px] uppercase tracking-[0.28em] text-[#475569]">#{{ str_pad($variant['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                                <p class="text-sm font-semibold text-[#0f172a]">{{ $variant['form'] }}</p>
                                                @if($variant['is_current'])
                                                    <span class="text-xs uppercase tracking-[0.3em] text-[#6d28d9]">Forma Atual</span>
                                                @endif
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:flex-1 bg-[#111827] p-6 lg:p-8 text-white">
                <div class="mb-8 rounded-[2rem] bg-[#0f172a] p-6 shadow-inner border border-white/10">
                    <h2 class="text-sm uppercase tracking-[0.32em] text-[#38bdf8]">Status</h2>
                    <div class="mt-5 space-y-4">
                        @foreach($pokemon['stats'] as $stat)
                            <div>
                                <div class="flex items-center justify-between text-sm text-[#cbd5e1] uppercase tracking-[0.18em]">
                                    <span>{{ str_replace('-', ' ', ucfirst($stat['stat']['name'])) }}</span>
                                    <span>{{ $stat['base_stat'] }}</span>
                                </div>
                                <div class="mt-2 h-3 rounded-full bg-[#1e293b] overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[#f59e0b] via-[#f97316] to-[#ef4444]" style="width: {{ min($stat['base_stat'], 100) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6">
                    <div class="rounded-[2rem] bg-[#0f172a] p-6 border border-white/10">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.32em] text-[#38bdf8]">Ataques</p>
                                <p class="mt-1 text-xs text-[#94a3b8]">Clique em um ataque para ver descrição e dano</p>
                            </div>
                        </div>
                        <div class="grid gap-3 max-h-[340px] overflow-y-auto pr-1">
                            @foreach($moves as $index => $move)
                                <button type="button" onclick="showMove({{ $index }})"
                                    class="w-full rounded-[1.5rem] border border-white/10 bg-white/5 px-4 py-3 text-left transition hover:bg-white/10">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-base font-semibold text-white">{{ $move['name'] }}</span>
                                        <span class="text-xs uppercase tracking-[0.28em] text-[#94a3b8]">{{ $move['type'] }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-[#cbd5e1]">{{ $move['category'] }} | Poder {{ $move['power'] }} | PP {{ $move['pp'] }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div id="move-detail" class="rounded-[2rem] bg-[#0b1120] p-6 border border-white/10">
                        <p class="text-sm uppercase tracking-[0.32em] text-[#38bdf8]">Detalhes do ataque selecionado</p>
                        <h3 id="move-name" class="mt-4 text-2xl font-bold text-white">Clique em um ataque</h3>
                        <p id="move-desc" class="mt-3 text-sm leading-6 text-[#cbd5e1]">O texto do ataque aparecerá aqui quando você clicar em um dos botões.</p>
                        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3 text-sm text-[#cbd5e1]">
                            <span id="move-power">Poder: ?</span>
                            <span id="move-accuracy">Precisão: ?</span>
                            <span id="move-pp">PP: ?</span>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button onclick="window.location.href = '/pokedex/jogo';"
                            class="rounded-full bg-[#ef4444] px-6 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-white transition hover:bg-[#dc2626]">Jogar Quem é Esse Pokémon?</button>
                        <button onclick="window.location.href = window.location.pathname;"
                            class="rounded-full bg-[#facc15] px-6 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-[#0f172a] transition hover:bg-[#fde68a]">Buscar Próximo Pokémon</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const moves = @json($moves);

        function selectEvolution(id) {
            if (!id) {
                return;
            }
            window.location.href = `?id=${id}`;
        }

        function selectVariant(id) {
            if (!id) {
                return;
            }
            window.location.href = `?id=${id}`;
        }

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }
            window.location.href = window.location.pathname;
        }

        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(function() {
                fetch(`/api/pokemon/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="px-4 py-3 text-center text-sm text-[#94a3b8]">Nenhum Pokémon encontrado</div>';
                            searchResults.classList.remove('hidden');
                            return;
                        }

                        searchResults.innerHTML = data.map(pokemon => `
                            <button type="button" onclick="window.location.href = '?id=${pokemon.id}'"
                                class="w-full px-4 py-3 text-left border-b border-[#e2e8f0] last:border-b-0 hover:bg-[#f8fafc] transition flex items-center gap-3">
                                <span class="text-xs font-semibold text-[#475569]">#${String(pokemon.id).padStart(3, '0')}</span>
                                <span class="text-sm font-semibold text-[#0f172a]">${pokemon.name}</span>
                            </button>
                        `).join('');

                        searchResults.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Erro na busca:', error);
                        searchResults.classList.add('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#search-input') && !e.target.closest('#search-results')) {
                searchResults.classList.add('hidden');
            }
        });

        function speakAttack(message) {
            if (!window.speechSynthesis) {
                return;
            }
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.lang = 'pt-BR';
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utterance);
        }

        function showMove(index) {
            const move = moves[index];
            if (!move) {
                return;
            }

            const title = document.getElementById('move-name');
            const desc = document.getElementById('move-desc');
            const power = document.getElementById('move-power');
            const accuracy = document.getElementById('move-accuracy');
            const pp = document.getElementById('move-pp');

            title.textContent = move.name;
            desc.textContent = move.effect;
            power.textContent = `Poder: ${move.power}`;
            accuracy.textContent = `Precisâo: ${move.accuracy}`;
            pp.textContent = `PP: ${move.pp}`;

            speakAttack(`${move.name}: ${move.effect}. Este ataque tem poder ${move.power} e precisão ${move.accuracy}.`);
        }

        window.addEventListener('DOMContentLoaded', function () {
            if (moves.length) {
                showMove(0);
            }
        });
    </script>
</body>

</html>
