<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fundir Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .page-bg {
            background:
                radial-gradient(circle at 15% 10%, rgba(239, 68, 68, 0.22), transparent 24%),
                radial-gradient(circle at 85% 12%, rgba(250, 204, 21, 0.22), transparent 26%),
                linear-gradient(180deg, #111827 0%, #020617 100%);
        }
    </style>
</head>

<body class="page-bg min-h-screen px-4 py-8 text-white">
    <main class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-[960px] flex-col justify-center">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-[#ef4444]">Fusion</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Fundir Pokemon</h1>
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
                Revise os Pokemon selecionados antes de fundir.
            </div>
        @endif

        <form method="POST" action="{{ route('pokemon.fusion.store') }}" enctype="multipart/form-data"
            class="rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Pokemon A</span>
                    <select name="pokemon_a" required
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#ef4444]">
                        <option value="">Selecione</option>
                        @foreach(collect($pokemonOptions)->groupBy('group') as $group => $options)
                            <optgroup label="{{ $group }}">
                                @foreach($options as $option)
                                    <option value="{{ $option['value'] }}" @selected(old('pokemon_a') === $option['value'])>{{ $option['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('pokemon_a') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Pokemon B</span>
                    <select name="pokemon_b" required
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#ef4444]">
                        <option value="">Selecione</option>
                        @foreach(collect($pokemonOptions)->groupBy('group') as $group => $options)
                            <optgroup label="{{ $group }}">
                                @foreach($options as $option)
                                    <option value="{{ $option['value'] }}" @selected(old('pokemon_b') === $option['value'])>{{ $option['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('pokemon_b') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Nome da fusao</span>
                    <input name="name" value="{{ old('name') }}" placeholder="Opcional; Groq pode gerar"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#ef4444]">
                </label>

                <label>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">URL da imagem</span>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="Opcional; Groq pode gerar SVG"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#ef4444]">
                    @error('image_url') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>

                <label class="md:col-span-2">
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Imagem da fusao</span>
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"
                        class="mt-2 w-full rounded-lg border border-[#cbd5e1] bg-white px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-[#ef4444]">
                    @error('image') <span class="mt-1 block text-xs font-bold text-[#dc2626]">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="mt-5 rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4">
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="use_ai" value="1" @checked(old('use_ai', $groqConfigured ? '1' : null)) @disabled(! $groqConfigured)
                        class="mt-1 h-4 w-4 accent-[#ef4444]">
                    <span>
                        <span class="block text-sm font-black">Usar Groq para sugerir a fusao</span>
                        <span class="mt-1 block text-sm font-medium text-[#475569]">
                            @if($groqConfigured)
                                A IA gera nome, descricao, stats, habilidades, golpes e uma ilustracao SVG se voce nao enviar imagem.
                            @else
                                Configure GROQ_API_KEY no .env para ativar essa opcao. Sem chave, a fusao usa a logica local.
                            @endif
                        </span>
                    </span>
                </label>
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <a href="{{ route('pokemon.create.choice') }}"
                    class="rounded-lg border border-[#cbd5e1] px-6 py-3 text-sm font-bold uppercase tracking-[0.18em] text-[#0f172a] transition hover:bg-[#f8fafc]">Cancelar</a>
                <button type="submit"
                    class="rounded-lg bg-[#ef4444] px-6 py-3 text-sm font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#dc2626]">Criar fusao</button>
            </div>
        </form>
    </main>
</body>

</html>
