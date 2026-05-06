<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuário API - Aula Prática</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pokedex-bg {
            background: radial-gradient(circle at top left, #38bdf8 0%, transparent 20%),
                radial-gradient(circle at top right, #0ea5e9 0%, transparent 20%),
                linear-gradient(180deg, #0f172a 0%, #111827 100%);
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

        .badge-pill {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body class="pokedex-bg min-h-screen flex flex-col items-center justify-center px-4 py-8">
    <div class="w-full max-w-[1200px] mb-6">
        <div class="relative">
            <input type="text" id="search-input" placeholder="Pesquise por nome ou ID..."
                class="w-full rounded-full bg-white px-6 py-3 text-sm font-medium text-[#0f172a] placeholder-[#94a3b8] shadow-lg focus:outline-none focus:ring-2 focus:ring-[#38bdf8]" />
            <div id="search-results"
                class="absolute top-full left-0 right-0 mt-2 rounded-[1.75rem] bg-white shadow-lg border border-[#e2e8f0] max-h-80 overflow-y-auto hidden z-50">
            </div>
        </div>
    </div>

    <div class="pokedex-card rounded-[2.5rem] max-w-[1200px] w-full overflow-hidden">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-0">
            <div class="lg:w-[360px] bg-[#f8fafc] p-6 lg:p-8 border-r border-[#cbd5e1] relative lg:sticky lg:top-8 self-start">
                <div class="relative z-10">
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-[0.32em] text-[#475569]">Usuário API</p>
                        <div class="mt-3 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.45em] text-[#475569]">ID</p>
                                <p class="text-2xl font-extrabold text-[#0f172a]">{{ str_pad($user['id'], 3, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative rounded-[2rem] bg-[#0f172a] p-4 shadow-inner pokedex-screen">
                        <img src="{{ $user['image'] ?? 'https://via.placeholder.com/300x300?text=Sem+imagem' }}" alt="{{ $user['fullName'] }}"
                            class="mx-auto h-56 w-auto rounded-[2rem] bg-white p-3 object-cover" />
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[1.75rem] bg-white p-4 shadow-sm border border-[#cbd5e1]">
                            <h2 class="text-sm uppercase tracking-[0.28em] text-[#475569]">Nome</h2>
                            <p class="mt-2 text-3xl font-bold text-[#0f172a]">{{ $user['fullName'] }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Idade</p>
                                <p class="mt-2 text-sm font-semibold text-[#0f172a]">{{ $user['age'] }} anos</p>
                            </div>
                            <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Gênero</p>
                                <p class="mt-2 text-sm font-semibold text-[#0f172a]">{{ ucfirst($user['gender']) }}</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Contato</p>
                            <div class="mt-3 space-y-2">
                                <p class="text-sm font-semibold text-[#0f172a]">{{ $user['email'] }}</p>
                                <p class="text-sm text-[#475569]">{{ $user['phone'] }}</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#111827] p-4 text-white">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Endereço</p>
                            <p class="mt-3 text-sm leading-6">{{ $user['address']['address'] ?? 'Não disponível' }}, {{ $user['address']['city'] ?? 'Não disponível' }}, {{ $user['address']['state'] ?? 'Não disponível' }}</p>
                            <p class="mt-1 text-sm text-[#cbd5e1]">CEP: {{ $user['address']['postalCode'] ?? 'Não disponível' }}</p>
                        </div>

                        <div class="rounded-[1.75rem] bg-[#e2e8f0] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#475569]">Empresa</p>
                            <p class="mt-2 text-sm font-semibold text-[#0f172a]">{{ $user['company']['name'] ?? 'Não disponível' }}</p>
                            <p class="text-sm text-[#475569]">{{ ($user['company']['department'] ?? 'Não disponível') }} - {{ ($user['company']['title'] ?? 'Não disponível') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:flex-1 bg-[#111827] p-6 lg:p-8 text-white">
                <div class="mb-8 rounded-[2rem] bg-[#0f172a] p-6 shadow-inner border border-white/10">
                    <h2 class="text-sm uppercase tracking-[0.32em] text-[#38bdf8]">Perfil</h2>
                    <div class="mt-5 space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[1.75rem] bg-[#111827] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Universidade</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $user['university'] ?? 'Não disponível' }}</p>
                            </div>
                            <div class="rounded-[1.75rem] bg-[#111827] p-4">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Tipo sanguêneo</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $user['bloodGroup'] ?? 'Não disponível' }}</p>
                            </div>
                        </div>
                        <div class="rounded-[1.75rem] bg-[#111827] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Empresa</p>
                            <p class="mt-2 text-sm leading-6 text-[#cbd5e1]">{{ $user['company']['name'] ?? 'Não disponível' }}, {{ $user['company']['title'] ?? 'Não disponível' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-[#0b1120] p-6 border border-white/10">
                    <h3 class="text-sm uppercase tracking-[0.32em] text-[#38bdf8]">Dados adicionais</h3>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.75rem] bg-[#111827] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">IP</p>
                            <p class="mt-2 text-sm text-[#cbd5e1]">{{ $user['ip'] ?? 'Não disponível' }}</p>
                        </div>
                        <div class="rounded-[1.75rem] bg-[#111827] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Domínio</p>
                            <p class="mt-2 text-sm text-[#cbd5e1]">{{ $user['domain'] ?? 'Não disponível' }}</p>
                        </div>
                        <div class="rounded-[1.75rem] bg-[#111827] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Peso</p>
                            <p class="mt-2 text-sm text-[#cbd5e1]">{{ $user['weight'] }} kg</p>
                        </div>
                        <div class="rounded-[1.75rem] bg-[#111827] p-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[#94a3b8]">Altura</p>
                            <p class="mt-2 text-sm text-[#cbd5e1]">{{ $user['height'] }} cm</p>
                        </div>
                    </div>
                </div>

                <button onclick="window.location.href = window.location.pathname;"
                    class="mt-6 w-full rounded-full bg-[#facc15] px-6 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-[#0f172a] transition hover:bg-[#fde68a]">Buscar Outro Usuário</button>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(searchTimeout);

            if (query.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(function () {
                fetch(`/api/usuarios/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            searchResults.innerHTML = '<div class="px-4 py-3 text-center text-sm text-[#94a3b8]">Nenhum usuário encontrado</div>';
                            searchResults.classList.remove('hidden');
                            return;
                        }

                        searchResults.innerHTML = data.map(user => `
                            <button type="button" onclick="window.location.href='?id=${user.id}'"
                                class="w-full px-4 py-3 text-left border-b border-[#e2e8f0] last:border-b-0 hover:bg-[#f8fafc] transition flex items-center gap-3">
                                <span class="text-xs font-semibold text-[#475569]">#${String(user.id).padStart(3, '0')}</span>
                                <span class="text-sm font-semibold text-[#0f172a]">${user.name}</span>
                            </button>
                        `).join('');

                        searchResults.classList.remove('hidden');
                    });
            }, 250);
        });
    </script>
</body>

</html>
