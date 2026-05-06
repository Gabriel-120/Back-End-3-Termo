<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                radial-gradient(circle at 18% 12%, rgba(34, 197, 94, 0.18), transparent 24%),
                radial-gradient(circle at 82% 10%, rgba(250, 204, 21, 0.22), transparent 26%),
                linear-gradient(180deg, #111827 0%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    <main class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-[960px] flex-col justify-center">
        <nav class="mb-8 flex flex-wrap gap-2">
            <a href="{{ route('pokemon.list') }}"
                class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Lista</a>
            <a href="{{ route('pokedex.show') }}"
                class="rounded-lg border border-white/20 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Pokedex</a>
            <a href="{{ route('pokemon.delete.index') }}"
                class="rounded-lg bg-[#ef4444] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Excluir criados</a>
        </nav>

        <section>
            <p class="text-xs font-black uppercase tracking-[0.3em] text-[#facc15]">Laboratorio Pokemon</p>
            <h1 class="mt-3 text-4xl font-black tracking-normal">O que voce quer criar?</h1>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <a href="{{ route('pokemon.create.form') }}"
                    class="rounded-lg border border-white/10 bg-white p-6 text-[#0f172a] shadow-2xl transition hover:-translate-y-1 hover:border-[#22c55e]">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-[#16a34a]">Criacao manual</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal">Criar um Pokemon</h2>
                    <p class="mt-4 text-sm font-medium leading-6 text-[#475569]">Preencha os dados principais da PokeAPI, stats, ataques, linha evolutiva e metadados extras.</p>
                </a>

                <a href="{{ route('pokemon.fusion.form') }}"
                    class="rounded-lg border border-white/10 bg-white p-6 text-[#0f172a] shadow-2xl transition hover:-translate-y-1 hover:border-[#ef4444]">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-[#ef4444]">Fusion</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal">Fundir Pokemon</h2>
                    <p class="mt-4 text-sm font-medium leading-6 text-[#475569]">Escolha dois Pokemon oficiais ou criados por voce. A fusao pode usar Groq se houver chave configurada.</p>
                </a>
            </div>
        </section>
    </main>
</body>

</html>
