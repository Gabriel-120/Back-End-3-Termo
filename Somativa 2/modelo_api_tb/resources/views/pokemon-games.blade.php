<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Jogos Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                linear-gradient(135deg, rgba(250, 204, 21, 0.14) 0 12%, transparent 12% 100%),
                linear-gradient(225deg, rgba(37, 99, 235, 0.16) 0 16%, transparent 16% 100%),
                linear-gradient(180deg, #0f172a 0%, #111827 58%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    <main class="mx-auto w-full max-w-[1100px]">
        <nav class="mb-8 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-[#facc15]">Pokedex / Jogos</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Central de Jogos</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokemon.list') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Lista</a>
                <a href="{{ route('pokedex.show') }}"
                    class="rounded-lg bg-[#ef4444] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Pokedex</a>
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Criar/Fundir</a>
            </div>
        </nav>

        <section class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('pokemon.game.guess') }}"
                class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-xl transition hover:-translate-y-1 hover:border-[#facc15]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">Quiz</p>
                        <h2 class="mt-2 text-2xl font-black tracking-normal">Quem e esse Pokemon?</h2>
                    </div>
                    <span class="rounded-lg bg-[#facc15] px-3 py-1 text-xs font-black uppercase text-[#0f172a]">Rapido</span>
                </div>
                <div class="mt-5 h-36 rounded-lg bg-[#e2e8f0] p-4">
                    <div class="grid h-full place-items-center rounded-lg border-2 border-dashed border-[#94a3b8] text-6xl font-black text-[#0f172a]">?</div>
                </div>
                <p class="mt-4 text-sm font-semibold leading-6 text-[#475569]">
                    Adivinhe o Pokemon pela silhueta, revele a resposta e jogue outra rodada quando quiser.
                </p>
            </a>

            <a href="{{ route('pokemon.game.fire-red') }}"
                class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-xl transition hover:-translate-y-1 hover:border-[#22c55e]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">RPG 2D</p>
                        <h2 class="mt-2 text-2xl font-black tracking-normal">Fire Red 2D</h2>
                    </div>
                    <span class="rounded-lg bg-[#22c55e] px-3 py-1 text-xs font-black uppercase text-[#052e16]">Novo</span>
                </div>
                <div class="mt-5 grid h-36 grid-cols-5 gap-1 rounded-lg bg-[#bbf7d0] p-3">
                    @for($i = 0; $i < 20; $i++)
                        <span class="rounded {{ in_array($i, [1, 2, 7, 12, 13, 18], true) ? 'bg-[#84cc16]' : (in_array($i, [4, 9, 14, 19], true) ? 'bg-[#60a5fa]' : 'bg-[#fde68a]') }}"></span>
                    @endfor
                </div>
                <p class="mt-4 text-sm font-semibold leading-6 text-[#475569]">
                    Escolha a geracao, pegue um inicial, explore a grama, batalhe, capture, suba de nivel e desbloqueie formas.
                </p>
            </a>

            <div class="rounded-lg border border-white/10 bg-white/10 p-5 text-[#cbd5e1] md:col-span-2">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-[#facc15]">Proximos slots</p>
                <p class="mt-2 text-sm leading-6">
                    Esta rota agora funciona como armazenamento de jogos. Novos modos podem entrar aqui sem trocar os links principais da Pokedex.
                </p>
            </div>
        </section>
    </main>
</body>

</html>
