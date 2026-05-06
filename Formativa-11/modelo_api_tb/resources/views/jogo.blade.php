<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo Quem é Esse Pokémon? - Aula Prática</title>
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

        .silhouette {
            filter: brightness(0) invert(1);
        }

        .revealed {
            filter: none;
        }
    </style>
</head>

<body class="pokedex-bg min-h-screen flex flex-col items-center justify-center px-4 py-8">
    <div class="pokedex-card rounded-[2.5rem] max-w-[800px] w-full overflow-hidden">
        <div class="flex flex-col items-center p-8">
            <h1 class="text-2xl font-bold text-[#0f172a] mb-6">Quem é Esse Pokémon?</h1>

            <div class="relative rounded-[2rem] bg-[#0f172a] p-8 shadow-inner pokedex-screen mb-8">
                <img id="pokemon-image" src="{{ $pokemon['sprites']['other']['official-artwork']['front_default'] }}"
                    alt="Pokémon" class="mx-auto h-48 w-auto silhouette" />
            </div>

            <div id="options" class="grid grid-cols-1 gap-4 w-full max-w-md mb-6">
                @foreach($options as $option)
                    <button type="button" onclick="checkAnswer('{{ $option }}')"
                        class="w-full rounded-full bg-[#2563eb] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1d4ed8]">
                        {{ $option }}
                    </button>
                @endforeach
            </div>

            <div id="result" class="text-center hidden">
                <h2 id="result-text" class="text-xl font-bold mb-4"></h2>
                <button onclick="window.location.reload()"
                    class="rounded-full bg-[#facc15] px-6 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-[#0f172a] transition hover:bg-[#fde68a]">
                    Jogar Novamente
                </button>
            </div>
        </div>
    </div>

    <script>
        const correctName = '{{ $correctName }}';
        const image = document.getElementById('pokemon-image');
        const options = document.getElementById('options');
        const result = document.getElementById('result');
        const resultText = document.getElementById('result-text');

        function checkAnswer(selected) {
            image.classList.remove('silhouette');
            image.classList.add('revealed');
            options.classList.add('hidden');
            result.classList.remove('hidden');

            if (selected === correctName) {
                resultText.textContent = `Correto! É o ${correctName}!`;
                resultText.className = 'text-xl font-bold text-green-600 mb-4';
            } else {
                resultText.textContent = `Errado! Era o ${correctName}.`;
                resultText.className = 'text-xl font-bold text-red-600 mb-4';
            }
        }
    </script>
</body>

</html>