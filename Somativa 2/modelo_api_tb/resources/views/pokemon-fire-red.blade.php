<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Red 2D</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.80.1/dist/phaser.min.js"></script>
    <style>
        .game-bg {
            background:
                linear-gradient(135deg, rgba(34, 197, 94, 0.13) 0 18%, transparent 18% 100%),
                linear-gradient(225deg, rgba(239, 68, 68, 0.13) 0 18%, transparent 18% 100%),
                linear-gradient(180deg, #0f172a 0%, #111827 55%, #020617 100%);
        }

        #game-root canvas {
            width: 100%;
            max-width: 704px;
            image-rendering: pixelated;
            border-radius: 8px;
        }

        .hp-bar {
            transition: width 220ms ease;
        }
    </style>
</head>

<body class="game-bg min-h-screen px-4 py-6 text-white">
    <main class="mx-auto w-full max-w-[1180px]">
        <nav class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-[#facc15]">RPG 2D</p>
                <h1 class="mt-2 text-3xl font-black tracking-normal">Fire Red 2D</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pokemon.game') }}"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#0f172a] transition hover:bg-[#fef3c7]">Jogos</a>
                <a href="{{ route('pokemon.list') }}"
                    class="rounded-lg bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1d4ed8]">Lista</a>
                <button id="reset-save" type="button"
                    class="rounded-lg bg-[#ef4444] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#dc2626]">Novo jogo</button>
            </div>
        </nav>

        <section class="grid gap-4 xl:grid-cols-[minmax(0,704px)_360px]">
            <div class="rounded-lg border border-white/10 bg-white/10 p-3 shadow-2xl backdrop-blur">
                <div id="game-root" class="overflow-hidden rounded-lg bg-[#0f172a]"></div>

                <div class="mt-3 grid grid-cols-3 gap-2 sm:hidden">
                    <span></span>
                    <button data-dir="up" class="move-btn rounded-lg bg-white px-4 py-3 font-black text-[#0f172a]">UP</button>
                    <span></span>
                    <button data-dir="left" class="move-btn rounded-lg bg-white px-4 py-3 font-black text-[#0f172a]">LEFT</button>
                    <button id="mobile-action" class="rounded-lg bg-[#facc15] px-4 py-3 font-black text-[#0f172a]">A</button>
                    <button data-dir="right" class="move-btn rounded-lg bg-white px-4 py-3 font-black text-[#0f172a]">RIGHT</button>
                    <span></span>
                    <button data-dir="down" class="move-btn rounded-lg bg-white px-4 py-3 font-black text-[#0f172a]">DOWN</button>
                    <span></span>
                </div>
            </div>

            <aside class="grid gap-4">
                <section class="rounded-lg border border-white/10 bg-white p-4 text-[#0f172a] shadow-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">Treinador</p>
                            <h2 id="trainer-region" class="mt-1 text-xl font-black">Escolha uma geracao</h2>
                        </div>
                        <span id="save-status" class="rounded-lg bg-[#e2e8f0] px-2 py-1 text-[10px] font-black uppercase text-[#475569]">Auto-save</span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
                        <div class="rounded-lg bg-[#f1f5f9] p-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Vistos</p>
                            <p id="seen-count" class="mt-1 text-lg font-black">0</p>
                        </div>
                        <div class="rounded-lg bg-[#f1f5f9] p-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Capturas</p>
                            <p id="caught-count" class="mt-1 text-lg font-black">0</p>
                        </div>
                        <div class="rounded-lg bg-[#f1f5f9] p-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#64748b]">Box</p>
                            <p id="box-count" class="mt-1 text-lg font-black">0</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-white/10 bg-white p-4 text-[#0f172a] shadow-xl">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-black uppercase tracking-[0.22em] text-[#64748b]">Equipe</h2>
                        <button id="heal-party" type="button"
                            class="rounded-lg bg-[#22c55e] px-3 py-2 text-xs font-black uppercase text-[#052e16] transition hover:bg-[#86efac]">Curar</button>
                    </div>
                    <div id="party-list" class="mt-3 grid gap-2"></div>
                </section>

                <section class="rounded-lg border border-white/10 bg-[#0f172a] p-4 shadow-xl">
                    <h2 class="text-sm font-black uppercase tracking-[0.22em] text-[#38bdf8]">Registro</h2>
                    <div id="game-log" class="mt-3 h-44 overflow-y-auto rounded-lg bg-[#020617] p-3 text-sm leading-6 text-[#cbd5e1]"></div>
                </section>
            </aside>
        </section>
    </main>

    <section id="starter-screen" class="fixed inset-0 z-40 grid place-items-center bg-[#020617]/92 p-4">
        <div class="w-full max-w-[980px] rounded-lg border border-white/10 bg-white p-5 text-[#0f172a] shadow-2xl">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">Inicio da jornada</p>
                    <h2 class="mt-2 text-3xl font-black tracking-normal">Escolha a geracao e o inicial</h2>
                </div>
                <a href="{{ route('pokemon.game') }}"
                    class="rounded-lg bg-[#0f172a] px-4 py-2 text-sm font-bold text-white">Voltar</a>
            </div>

            <div id="generation-tabs" class="mt-5 flex flex-wrap gap-2"></div>
            <div id="starter-options" class="mt-5 grid gap-4 md:grid-cols-3"></div>
        </div>
    </section>

    <section id="battle-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#020617]/88 p-4">
        <div class="grid w-full max-w-[980px] gap-4 rounded-lg border border-white/10 bg-white p-4 text-[#0f172a] shadow-2xl lg:grid-cols-[1fr_1fr]">
            <div class="rounded-lg bg-[#dbeafe] p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#475569]">Pokemon selvagem</p>
                        <h2 id="wild-name" class="mt-1 text-2xl font-black">---</h2>
                    </div>
                    <span id="wild-types" class="text-right text-xs font-black uppercase text-[#475569]"></span>
                </div>
                <div class="mt-4 grid place-items-center rounded-lg bg-white/80 p-4">
                    <img id="wild-sprite" src="" alt="Pokemon selvagem" class="h-44 w-44 object-contain">
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-xs font-black uppercase tracking-[0.18em] text-[#475569]">
                        <span>HP</span>
                        <span id="wild-hp-text">0/0</span>
                    </div>
                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-[#bfdbfe]">
                        <div id="wild-hp-bar" class="hp-bar h-full rounded-full bg-[#22c55e]" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-[#f8fafc] p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#475569]">Seu Pokemon</p>
                        <h2 id="active-name" class="mt-1 text-2xl font-black">---</h2>
                    </div>
                    <span id="active-types" class="text-right text-xs font-black uppercase text-[#475569]"></span>
                </div>
                <div class="mt-4 grid place-items-center rounded-lg bg-white p-4">
                    <img id="active-sprite" src="" alt="Seu Pokemon" class="h-36 w-36 object-contain">
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-xs font-black uppercase tracking-[0.18em] text-[#475569]">
                        <span>HP</span>
                        <span id="active-hp-text">0/0</span>
                    </div>
                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-[#cbd5e1]">
                        <div id="active-hp-bar" class="hp-bar h-full rounded-full bg-[#22c55e]" style="width: 100%"></div>
                    </div>
                </div>

                <div id="move-buttons" class="mt-4 grid gap-2 sm:grid-cols-2"></div>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    <button id="capture-btn" type="button" class="rounded-lg bg-[#facc15] px-4 py-3 text-sm font-black uppercase text-[#0f172a]">Capturar</button>
                    <button id="run-btn" type="button" class="rounded-lg bg-[#64748b] px-4 py-3 text-sm font-black uppercase text-white">Fugir</button>
                    <button data-form="mega" type="button" class="form-btn rounded-lg bg-[#ef4444] px-4 py-3 text-sm font-black uppercase text-white">Mega</button>
                    <button data-form="gmax" type="button" class="form-btn rounded-lg bg-[#8b5cf6] px-4 py-3 text-sm font-black uppercase text-white">Gigantamax</button>
                    <button data-form="alt" type="button" class="form-btn rounded-lg bg-[#14b8a6] px-4 py-3 text-sm font-black uppercase text-white sm:col-span-2">Forma alternativa</button>
                </div>
            </div>
        </div>
    </section>

    <section id="learn-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#020617]/90 p-4">
        <div class="w-full max-w-[520px] rounded-lg bg-white p-5 text-[#0f172a] shadow-2xl">
            <p class="text-xs font-black uppercase tracking-[0.24em] text-[#64748b]">Novo golpe</p>
            <h2 id="learn-title" class="mt-2 text-2xl font-black">---</h2>
            <p id="learn-description" class="mt-3 text-sm font-semibold leading-6 text-[#475569]"></p>
            <label id="replace-wrapper" class="mt-4 block hidden">
                <span class="text-xs font-black uppercase tracking-[0.2em] text-[#64748b]">Substituir golpe</span>
                <select id="replace-move" class="mt-2 w-full rounded-lg border border-[#cbd5e1] px-4 py-3 text-sm font-bold"></select>
            </label>
            <div class="mt-5 flex flex-wrap gap-2">
                <button id="learn-accept" type="button" class="rounded-lg bg-[#22c55e] px-5 py-3 text-sm font-black uppercase text-[#052e16]">Aprender</button>
                <button id="learn-skip" type="button" class="rounded-lg bg-[#e2e8f0] px-5 py-3 text-sm font-black uppercase text-[#0f172a]">Pular</button>
            </div>
        </div>
    </section>

    <script>
        const CUSTOM_POKEMON = @json($customPokemonForGame);
        const SAVE_KEY = 'pokemonFireRed2D.save.v1';
        const TILE = 32;
        const MAP_WIDTH = 22;
        const MAP_HEIGHT = 16;
        const REGION_NAMES = {
            1: 'Kanto',
            2: 'Johto',
            3: 'Hoenn',
            4: 'Sinnoh',
            5: 'Unova',
            6: 'Kalos',
            7: 'Alola',
            8: 'Galar/Hisui',
            9: 'Paldea'
        };
        const STARTERS = {
            1: [
                { id: 1, name: 'Bulbasaur', types: ['grass', 'poison'] },
                { id: 4, name: 'Charmander', types: ['fire'] },
                { id: 7, name: 'Squirtle', types: ['water'] }
            ],
            2: [
                { id: 152, name: 'Chikorita', types: ['grass'] },
                { id: 155, name: 'Cyndaquil', types: ['fire'] },
                { id: 158, name: 'Totodile', types: ['water'] }
            ],
            3: [
                { id: 252, name: 'Treecko', types: ['grass'] },
                { id: 255, name: 'Torchic', types: ['fire'] },
                { id: 258, name: 'Mudkip', types: ['water'] }
            ],
            4: [
                { id: 387, name: 'Turtwig', types: ['grass'] },
                { id: 390, name: 'Chimchar', types: ['fire'] },
                { id: 393, name: 'Piplup', types: ['water'] }
            ],
            5: [
                { id: 495, name: 'Snivy', types: ['grass'] },
                { id: 498, name: 'Tepig', types: ['fire'] },
                { id: 501, name: 'Oshawott', types: ['water'] }
            ],
            6: [
                { id: 650, name: 'Chespin', types: ['grass'] },
                { id: 653, name: 'Fennekin', types: ['fire'] },
                { id: 656, name: 'Froakie', types: ['water'] }
            ],
            7: [
                { id: 722, name: 'Rowlet', types: ['grass', 'flying'] },
                { id: 725, name: 'Litten', types: ['fire'] },
                { id: 728, name: 'Popplio', types: ['water'] }
            ],
            8: [
                { id: 810, name: 'Grookey', types: ['grass'] },
                { id: 813, name: 'Scorbunny', types: ['fire'] },
                { id: 816, name: 'Sobble', types: ['water'] }
            ],
            9: [
                { id: 906, name: 'Sprigatito', types: ['grass'] },
                { id: 909, name: 'Fuecoco', types: ['fire'] },
                { id: 912, name: 'Quaxly', types: ['water'] }
            ]
        };
        const WILD_POOLS = {
            1: [10, 13, 16, 19, 21, 25, 29, 32, 39, 41, 43, 56, 63, 66, 74, 92, 129],
            2: [161, 163, 165, 167, 172, 179, 187, 190, 194, 198, 200, 204, 209, 215, 216],
            3: [261, 263, 265, 270, 273, 276, 278, 280, 283, 285, 287, 293, 300, 304, 309],
            4: [399, 401, 403, 406, 418, 425, 427, 431, 434, 436, 443, 447, 449, 451, 453],
            5: [504, 506, 509, 511, 513, 515, 519, 522, 524, 527, 529, 532, 535, 540, 543],
            6: [659, 661, 664, 667, 669, 674, 677, 679, 686, 688, 692, 694, 701, 704, 707],
            7: [731, 734, 736, 739, 742, 744, 747, 751, 753, 755, 757, 759, 761, 769, 775],
            8: [819, 821, 824, 827, 829, 831, 833, 835, 837, 840, 843, 846, 848, 852, 859],
            9: [915, 917, 919, 921, 924, 926, 928, 931, 932, 935, 938, 940, 944, 948, 953]
        };
        const TYPE_LABELS = {
            normal: 'Normal',
            fire: 'Fogo',
            water: 'Agua',
            electric: 'Eletrico',
            grass: 'Planta',
            ice: 'Gelo',
            fighting: 'Lutador',
            poison: 'Veneno',
            ground: 'Terra',
            flying: 'Voador',
            psychic: 'Psiquico',
            bug: 'Inseto',
            rock: 'Pedra',
            ghost: 'Fantasma',
            dragon: 'Dragao',
            dark: 'Sombrio',
            steel: 'Metal',
            fairy: 'Fada'
        };
        const TYPE_COLORS = {
            normal: '#A8A77A',
            fire: '#EE8130',
            water: '#6390F0',
            electric: '#F7D02C',
            grass: '#7AC74C',
            ice: '#96D9D6',
            fighting: '#C22E28',
            poison: '#A33EA1',
            ground: '#E2BF65',
            flying: '#A98FF3',
            psychic: '#F95587',
            bug: '#A6B91A',
            rock: '#B6A136',
            ghost: '#735797',
            dragon: '#6F35FC',
            dark: '#705746',
            steel: '#B7B7CE',
            fairy: '#D685AD'
        };
        const TYPE_MOVES = {
            normal: [
                { level: 4, name: 'Investida Forte', type: 'normal', power: 45, accuracy: 100 },
                { level: 14, name: 'Ataque Rapido', type: 'normal', power: 55, accuracy: 100 }
            ],
            fire: [
                { level: 7, name: 'Brasa', type: 'fire', power: 50, accuracy: 100 },
                { level: 18, name: 'Chama Viva', type: 'fire', power: 75, accuracy: 95 },
                { level: 34, name: 'Explosao Solar', type: 'fire', power: 105, accuracy: 90 }
            ],
            water: [
                { level: 7, name: 'Jato de Agua', type: 'water', power: 50, accuracy: 100 },
                { level: 18, name: 'Onda Azul', type: 'water', power: 75, accuracy: 95 },
                { level: 34, name: 'Tsunami', type: 'water', power: 105, accuracy: 90 }
            ],
            grass: [
                { level: 7, name: 'Folha Navalha', type: 'grass', power: 50, accuracy: 100 },
                { level: 18, name: 'Raizes Vivas', type: 'grass', power: 75, accuracy: 95 },
                { level: 34, name: 'Floresta Final', type: 'grass', power: 105, accuracy: 90 }
            ],
            electric: [
                { level: 9, name: 'Choque', type: 'electric', power: 55, accuracy: 100 },
                { level: 24, name: 'Raio Duplo', type: 'electric', power: 90, accuracy: 92 }
            ],
            psychic: [
                { level: 9, name: 'Confusao', type: 'psychic', power: 55, accuracy: 100 },
                { level: 24, name: 'Pulso Mental', type: 'psychic', power: 90, accuracy: 92 }
            ],
            dark: [
                { level: 10, name: 'Mordida Sombria', type: 'dark', power: 60, accuracy: 100 },
                { level: 28, name: 'Noite Total', type: 'dark', power: 95, accuracy: 90 }
            ],
            dragon: [
                { level: 12, name: 'Sopro Draconico', type: 'dragon', power: 65, accuracy: 95 },
                { level: 38, name: 'Cometa Dragao', type: 'dragon', power: 115, accuracy: 88 }
            ]
        };
        const UNIVERSAL_MOVES = [
            { level: 5, name: 'Foco de Batalha', type: 'normal', power: 0, accuracy: 100, status: 'focus' },
            { level: 12, name: 'Golpe Preciso', type: 'normal', power: 65, accuracy: 100 },
            { level: 22, name: 'Ataque Heroico', type: 'normal', power: 85, accuracy: 95 },
            { level: 30, name: 'Energia Mega', type: 'normal', power: 0, accuracy: 100, status: 'mega' },
            { level: 45, name: 'Pulso Gigante', type: 'normal', power: 110, accuracy: 90 }
        ];
        const DEFAULT_STATS = {
            hp: 48,
            attack: 52,
            defense: 48,
            speed: 50
        };

        let selectedGeneration = 1;
        let game = null;
        let player = null;
        let cursors = null;
        let wasd = null;
        let mobileDirection = null;
        let lastTile = null;
        let encounterStarting = false;
        let battle = null;
        let pendingLearn = [];
        let learnModalOpen = false;
        let pokedexCache = {};
        let state = normalizeState(loadSave() || freshState());

        const starterScreen = document.getElementById('starter-screen');
        const generationTabs = document.getElementById('generation-tabs');
        const starterOptions = document.getElementById('starter-options');
        const partyList = document.getElementById('party-list');
        const gameLog = document.getElementById('game-log');
        const battleModal = document.getElementById('battle-modal');
        const learnModal = document.getElementById('learn-modal');

        document.getElementById('reset-save').addEventListener('click', () => {
            if (!confirm('Comecar um novo jogo e apagar o progresso salvo neste navegador?')) {
                return;
            }
            localStorage.removeItem(SAVE_KEY);
            state = freshState();
            pendingLearn = [];
            battle = null;
            encounterStarting = false;
            battleModal.classList.add('hidden');
            battleModal.classList.remove('flex');
            learnModal.classList.add('hidden');
            learnModal.classList.remove('flex');
            learnModalOpen = false;
            showStarterScreen();
            renderAll();
            if (player) {
                player.x = state.player.x;
                player.y = state.player.y;
            }
            logMessage('Novo jogo iniciado. Escolha sua geracao.');
        });

        document.getElementById('heal-party').addEventListener('click', () => {
            healParty();
            logMessage('Sua equipe foi curada.');
        });

        document.getElementById('capture-btn').addEventListener('click', captureWild);
        document.getElementById('run-btn').addEventListener('click', runFromBattle);
        document.getElementById('learn-accept').addEventListener('click', acceptLearnMove);
        document.getElementById('learn-skip').addEventListener('click', skipLearnMove);
        document.getElementById('mobile-action').addEventListener('click', interactTile);

        document.querySelectorAll('.form-btn').forEach((button) => {
            button.addEventListener('click', () => applyBattleForm(button.dataset.form));
        });

        document.querySelectorAll('.move-btn').forEach((button) => {
            button.addEventListener('pointerdown', () => mobileDirection = button.dataset.dir);
            button.addEventListener('pointerup', () => mobileDirection = null);
            button.addEventListener('pointerleave', () => mobileDirection = null);
        });

        renderStarterChoices();
        renderAll();

        if (state.party.length === 0) {
            showStarterScreen();
        } else {
            starterScreen.classList.add('hidden');
        }

        if (window.Phaser) {
            bootGame();
        } else {
            document.getElementById('game-root').innerHTML = '<div class="grid h-[420px] place-items-center p-6 text-center text-sm text-[#cbd5e1]">Nao foi possivel carregar o motor 2D. Verifique a conexao com a CDN do Phaser.</div>';
        }

        function freshState() {
            return {
                generation: null,
                party: [],
                box: [],
                activeIndex: 0,
                player: { x: TILE * 5 + TILE / 2, y: TILE * 9 + TILE / 2 },
                seen: 0,
                caught: 0,
                log: []
            };
        }

        function loadSave() {
            try {
                const raw = localStorage.getItem(SAVE_KEY);
                return raw ? JSON.parse(raw) : null;
            } catch (error) {
                return null;
            }
        }

        function normalizeState(loaded) {
            const base = freshState();
            const normalized = {
                ...base,
                ...loaded,
                player: { ...base.player, ...(loaded.player || {}) },
                party: Array.isArray(loaded.party) ? loaded.party.map(normalizeSavedPokemon) : [],
                box: Array.isArray(loaded.box) ? loaded.box.map(normalizeSavedPokemon) : [],
                log: Array.isArray(loaded.log) ? loaded.log : []
            };
            normalized.activeIndex = Math.min(Math.max(0, Number(normalized.activeIndex || 0)), Math.max(0, normalized.party.length - 1));
            return normalized;
        }

        function normalizeSavedPokemon(pokemon) {
            return {
                ...pokemon,
                uuid: pokemon.uuid || `${Date.now()}-${Math.random().toString(16).slice(2)}`,
                types: pokemon.types?.length ? pokemon.types : ['normal'],
                moves: pokemon.moves?.length ? pokemon.moves : buildStartingMoves(pokemon.types?.[0] || 'normal', []),
                learned: pokemon.learned?.length ? pokemon.learned : (pokemon.moves || []).map((move) => moveKey(move)),
                hp: Number(pokemon.hp ?? pokemon.maxHp ?? 1),
                maxHp: Number(pokemon.maxHp ?? pokemon.hp ?? 1),
                attack: Number(pokemon.attack ?? DEFAULT_STATS.attack),
                defense: Number(pokemon.defense ?? DEFAULT_STATS.defense),
                speed: Number(pokemon.speed ?? DEFAULT_STATS.speed),
                level: Number(pokemon.level ?? 5),
                exp: Number(pokemon.exp ?? 0),
                altForm: Boolean(pokemon.altForm),
                tempForm: null
            };
        }

        function saveGame() {
            localStorage.setItem(SAVE_KEY, JSON.stringify(state));
            const saveStatus = document.getElementById('save-status');
            saveStatus.textContent = 'Salvo';
            setTimeout(() => saveStatus.textContent = 'Auto-save', 900);
        }

        function officialArt(id) {
            return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${id}.png`;
        }

        function pixelSprite(id) {
            return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${id}.png`;
        }

        function showStarterScreen() {
            starterScreen.classList.remove('hidden');
            starterScreen.classList.add('grid');
        }

        function renderStarterChoices() {
            generationTabs.innerHTML = Object.keys(STARTERS).map((generation) => `
                <button type="button" data-generation="${generation}"
                    class="generation-tab rounded-lg px-3 py-2 text-xs font-black uppercase tracking-[0.16em] ${Number(generation) === selectedGeneration ? 'bg-[#facc15] text-[#0f172a]' : 'bg-[#e2e8f0] text-[#475569]'}">
                    ${REGION_NAMES[generation]}
                </button>
            `).join('');

            generationTabs.querySelectorAll('.generation-tab').forEach((button) => {
                button.addEventListener('click', () => {
                    selectedGeneration = Number(button.dataset.generation);
                    renderStarterChoices();
                });
            });

            starterOptions.innerHTML = STARTERS[selectedGeneration].map((starter) => `
                <button type="button" data-starter="${starter.id}"
                    class="starter-card rounded-lg border border-[#cbd5e1] bg-[#f8fafc] p-4 text-left transition hover:-translate-y-1 hover:border-[#facc15] hover:bg-white">
                    <div class="grid place-items-center rounded-lg bg-white p-3">
                        <img src="${officialArt(starter.id)}" alt="${starter.name}" class="h-40 w-40 object-contain">
                    </div>
                    <p class="mt-4 text-xs font-black uppercase tracking-[0.22em] text-[#64748b]">#${String(starter.id).padStart(3, '0')}</p>
                    <h3 class="mt-1 text-2xl font-black">${starter.name}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">${typePills(starter.types)}</div>
                </button>
            `).join('');

            starterOptions.querySelectorAll('.starter-card').forEach((button) => {
                button.addEventListener('click', () => chooseStarter(Number(button.dataset.starter)));
            });
        }

        async function chooseStarter(id) {
            const fallback = STARTERS[selectedGeneration].find((starter) => starter.id === id);
            const data = await loadPokemonData(fallback);
            const starter = makePokemon(data, 5, true);
            state = freshState();
            state.generation = selectedGeneration;
            state.party = [starter];
            state.activeIndex = 0;
            state.caught = 1;
            state.seen = 1;
            starterScreen.classList.add('hidden');
            starterScreen.classList.remove('grid');
            logMessage(`${starter.name} entrou na sua equipe inicial.`);
            renderAll();
            saveGame();
        }

        function bootGame() {
            const config = {
                type: Phaser.AUTO,
                width: MAP_WIDTH * TILE,
                height: MAP_HEIGHT * TILE,
                parent: 'game-root',
                backgroundColor: '#0f172a',
                pixelArt: true,
                physics: { default: 'arcade' },
                scene: {
                    create() {
                        drawMap(this);
                        player = this.add.container(state.player.x, state.player.y);
                        const body = this.add.graphics();
                        body.fillStyle(0xef4444, 1);
                        body.fillRoundedRect(-9, -12, 18, 24, 5);
                        body.fillStyle(0xf8fafc, 1);
                        body.fillRect(-7, -10, 14, 7);
                        body.fillStyle(0x0f172a, 1);
                        body.fillCircle(-3, -1, 2);
                        body.fillCircle(3, -1, 2);
                        player.add(body);
                        cursors = this.input.keyboard.createCursorKeys();
                        wasd = this.input.keyboard.addKeys('W,A,S,D,SPACE,E');
                        lastTile = tileAt(player.x, player.y);
                        logMessage('Use WASD ou setas para andar. A grama pode iniciar batalhas.');
                    },
                    update(_time, delta) {
                        updatePlayer(delta);
                    }
                }
            };

            game = new Phaser.Game(config);
        }

        function buildMap() {
            const map = [];
            for (let y = 0; y < MAP_HEIGHT; y++) {
                let row = '';
                for (let x = 0; x < MAP_WIDTH; x++) {
                    if (x === 0 || y === 0 || x === MAP_WIDTH - 1 || y === MAP_HEIGHT - 1) {
                        row += 'T';
                    } else if (x >= 15 && y >= 10) {
                        row += 'W';
                    } else if (x >= 2 && x <= 8 && y >= 2 && y <= 6) {
                        row += 'G';
                    } else if (x >= 11 && x <= 18 && y >= 2 && y <= 7) {
                        row += 'G';
                    } else if (x >= 4 && x <= 8 && y >= 11 && y <= 13) {
                        row += 'G';
                    } else if (x >= 2 && x <= 5 && y >= 8 && y <= 10) {
                        row += 'C';
                    } else if (x === 10 || y === 9) {
                        row += 'P';
                    } else {
                        row += 'D';
                    }
                }
                map.push(row);
            }
            return map;
        }

        const MAP = buildMap();

        function drawMap(scene) {
            const graphics = scene.add.graphics();
            const colors = {
                T: 0x166534,
                G: 0x65a30d,
                P: 0xfde68a,
                D: 0xbbf7d0,
                W: 0x60a5fa,
                C: 0xf8fafc
            };

            for (let y = 0; y < MAP_HEIGHT; y++) {
                for (let x = 0; x < MAP_WIDTH; x++) {
                    const tile = MAP[y][x];
                    graphics.fillStyle(colors[tile] || 0xbbf7d0, 1);
                    graphics.fillRect(x * TILE, y * TILE, TILE, TILE);
                    graphics.lineStyle(1, 0x0f172a, 0.08);
                    graphics.strokeRect(x * TILE, y * TILE, TILE, TILE);

                    if (tile === 'G') {
                        graphics.lineStyle(2, 0x365314, 0.45);
                        graphics.lineBetween(x * TILE + 8, y * TILE + 24, x * TILE + 12, y * TILE + 13);
                        graphics.lineBetween(x * TILE + 21, y * TILE + 24, x * TILE + 18, y * TILE + 12);
                    }

                    if (tile === 'W') {
                        graphics.lineStyle(2, 0x1d4ed8, 0.35);
                        graphics.lineBetween(x * TILE + 4, y * TILE + 12, x * TILE + 28, y * TILE + 12);
                        graphics.lineBetween(x * TILE + 4, y * TILE + 22, x * TILE + 28, y * TILE + 22);
                    }
                }
            }

            graphics.fillStyle(0xef4444, 1);
            graphics.fillRect(2 * TILE + 5, 8 * TILE + 2, 4 * TILE - 10, TILE - 4);
            graphics.fillStyle(0xffffff, 1);
            graphics.fillRect(2 * TILE + 10, 9 * TILE + 2, 4 * TILE - 20, TILE * 2 - 6);
            graphics.fillStyle(0x2563eb, 1);
            graphics.fillRect(3 * TILE + 8, 10 * TILE + 2, TILE - 16, TILE - 4);
        }

        function updatePlayer(delta) {
            if (!player || state.party.length === 0 || battle || learnModalOpen || !starterScreen.classList.contains('hidden')) {
                return;
            }

            if ((wasd?.SPACE?.isDown || wasd?.E?.isDown) && tileAt(player.x, player.y) === 'C') {
                interactTile();
                return;
            }

            const speed = 132;
            let dx = 0;
            let dy = 0;

            if (cursors.left.isDown || wasd.A.isDown || mobileDirection === 'left') {
                dx = -1;
            } else if (cursors.right.isDown || wasd.D.isDown || mobileDirection === 'right') {
                dx = 1;
            } else if (cursors.up.isDown || wasd.W.isDown || mobileDirection === 'up') {
                dy = -1;
            } else if (cursors.down.isDown || wasd.S.isDown || mobileDirection === 'down') {
                dy = 1;
            }

            if (dx === 0 && dy === 0) {
                return;
            }

            const distance = speed * (delta / 1000);
            const nextX = Phaser.Math.Clamp(player.x + dx * distance, TILE / 2, MAP_WIDTH * TILE - TILE / 2);
            const nextY = Phaser.Math.Clamp(player.y + dy * distance, TILE / 2, MAP_HEIGHT * TILE - TILE / 2);

            if (canMoveTo(nextX, nextY)) {
                player.x = nextX;
                player.y = nextY;
                state.player = { x: player.x, y: player.y };

                const currentTile = tileAt(player.x, player.y);
                if (currentTile !== lastTile) {
                    lastTile = currentTile;
                    saveGame();
                    if (currentTile === 'G') {
                        maybeStartEncounter();
                    }
                }
            }
        }

        function tileAt(x, y) {
            const tx = Math.max(0, Math.min(MAP_WIDTH - 1, Math.floor(x / TILE)));
            const ty = Math.max(0, Math.min(MAP_HEIGHT - 1, Math.floor(y / TILE)));
            return MAP[ty][tx];
        }

        function canMoveTo(x, y) {
            const points = [
                [x - 9, y - 11],
                [x + 9, y - 11],
                [x - 9, y + 11],
                [x + 9, y + 11]
            ];

            return points.every(([px, py]) => {
                const tile = tileAt(px, py);
                return tile !== 'T' && tile !== 'W';
            });
        }

        function interactTile() {
            if (!player) {
                return;
            }

            if (tileAt(player.x, player.y) === 'C') {
                healParty();
                logMessage('Centro Pokemon: equipe curada.');
                return;
            }

            logMessage('Nada para interagir aqui.');
        }

        function maybeStartEncounter() {
            if (encounterStarting || battle || Math.random() > 0.16) {
                return;
            }

            encounterStarting = true;
            startEncounter().finally(() => {
                encounterStarting = false;
            });
        }

        async function startEncounter() {
            const active = getActivePokemon();
            if (!active || active.hp <= 0) {
                logMessage('Nenhum Pokemon pronto para batalhar.');
                return;
            }

            const entry = pickWildEntry();
            const level = Math.max(2, active.level + Phaser.Math.Between(-2, 3));
            const data = await loadPokemonData(entry);
            const wild = makePokemon(data, level, false);
            battle = {
                wild,
                locked: false,
                usedMega: false,
                usedGmax: false
            };
            state.seen += 1;
            logMessage(`Um ${wild.name} selvagem apareceu!`);
            renderAll();
            renderBattle();
            battleModal.classList.remove('hidden');
            battleModal.classList.add('flex');
            saveGame();
        }

        function pickWildEntry() {
            if (CUSTOM_POKEMON.length > 0 && Math.random() < 0.22) {
                return { ...CUSTOM_POKEMON[Math.floor(Math.random() * CUSTOM_POKEMON.length)], custom: true };
            }

            const pool = WILD_POOLS[state.generation || 1] || WILD_POOLS[1];
            return pool[Math.floor(Math.random() * pool.length)];
        }

        async function loadPokemonData(entry) {
            if (typeof entry === 'object' && entry.custom) {
                return {
                    id: entry.id,
                    name: entry.name,
                    types: entry.types?.length ? entry.types : ['normal'],
                    sprite: entry.image || placeholderImage(entry.name),
                    stats: normalizeGameStats(entry.stats),
                    moves: entry.moves?.length ? entry.moves : [],
                    isCustom: true
                };
            }

            const fallback = typeof entry === 'object' ? entry : { id: entry, name: `Pokemon #${entry}`, types: ['normal'] };
            const id = fallback.id;

            if (pokedexCache[id]) {
                return pokedexCache[id];
            }

            try {
                const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${id}`);
                if (!response.ok) {
                    throw new Error('PokeAPI indisponivel');
                }
                const api = await response.json();
                const data = {
                    id,
                    name: titleCase(api.name),
                    types: api.types.map((item) => item.type.name),
                    sprite: api.sprites?.front_default || pixelSprite(id),
                    stats: normalizeGameStats(Object.fromEntries(api.stats.map((item) => [item.stat.name, item.base_stat]))),
                    moves: api.moves.slice(0, 5).map((item) => titleCase(item.move.name)),
                    isCustom: false
                };
                pokedexCache[id] = data;
                return data;
            } catch (error) {
                return {
                    id,
                    name: fallback.name || `Pokemon #${id}`,
                    types: fallback.types || ['normal'],
                    sprite: pixelSprite(id),
                    stats: DEFAULT_STATS,
                    moves: [],
                    isCustom: false
                };
            }
        }

        function makePokemon(data, level, owned) {
            const stats = normalizeGameStats(data.stats);
            const maxHp = Math.round(18 + stats.hp * 0.65 + level * 5);
            const primaryType = data.types?.[0] || 'normal';
            const startingMoves = buildStartingMoves(primaryType, data.moves);

            return {
                uuid: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
                id: data.id,
                name: data.name,
                types: data.types?.length ? data.types : ['normal'],
                sprite: data.sprite || placeholderImage(data.name),
                level,
                exp: 0,
                hp: maxHp,
                maxHp,
                attack: Math.round(8 + stats.attack * 0.48 + level * 2.2),
                defense: Math.round(8 + stats.defense * 0.42 + level * 1.9),
                speed: Math.round(8 + stats.speed * 0.44 + level * 2),
                moves: startingMoves,
                learned: startingMoves.map((move) => moveKey(move)),
                altForm: false,
                tempForm: null,
                owned,
                isCustom: data.isCustom || false
            };
        }

        function normalizeGameStats(stats) {
            if (!stats || typeof stats !== 'object') {
                return { ...DEFAULT_STATS };
            }

            return {
                hp: Number(stats.hp ?? DEFAULT_STATS.hp),
                attack: Number(stats.attack ?? DEFAULT_STATS.attack),
                defense: Number(stats.defense ?? DEFAULT_STATS.defense),
                speed: Number(stats.speed ?? DEFAULT_STATS.speed)
            };
        }

        function buildStartingMoves(primaryType, apiMoves) {
            const firstApiMove = apiMoves?.[0] ? titleCase(apiMoves[0]) : 'Investida';
            const typeName = TYPE_LABELS[primaryType] || titleCase(primaryType);

            return [
                { name: firstApiMove, type: 'normal', power: 40, accuracy: 100 },
                { name: `Pulso ${typeName}`, type: primaryType, power: 48, accuracy: 98 }
            ];
        }

        function getActivePokemon() {
            if (!state.party.length) {
                return null;
            }

            if (!state.party[state.activeIndex] || state.party[state.activeIndex].hp <= 0) {
                const next = state.party.findIndex((pokemon) => pokemon.hp > 0);
                state.activeIndex = next >= 0 ? next : 0;
            }

            return state.party[state.activeIndex] || null;
        }

        function renderAll() {
            renderTrainer();
            renderParty();
            renderLog();
        }

        function renderTrainer() {
            document.getElementById('trainer-region').textContent = state.generation ? `Regiao ${REGION_NAMES[state.generation]}` : 'Escolha uma geracao';
            document.getElementById('seen-count').textContent = state.seen;
            document.getElementById('caught-count').textContent = state.caught;
            document.getElementById('box-count').textContent = state.box.length;
        }

        function renderParty() {
            if (!state.party.length) {
                partyList.innerHTML = '<p class="rounded-lg bg-[#f1f5f9] p-3 text-sm font-semibold text-[#64748b]">Nenhum Pokemon escolhido ainda.</p>';
                return;
            }

            partyList.innerHTML = state.party.map((pokemon, index) => {
                const hpPercent = hpPercentValue(pokemon);
                const isActive = index === state.activeIndex;
                return `
                    <div class="rounded-lg border ${isActive ? 'border-[#facc15] bg-[#fffbeb]' : 'border-[#e2e8f0] bg-[#f8fafc]'} p-3">
                        <div class="flex items-center gap-3">
                            <img src="${pokemon.sprite}" alt="${pokemon.name}" class="h-12 w-12 rounded-lg bg-white object-contain">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-black">${displayMonName(pokemon)}</p>
                                    <span class="text-xs font-black text-[#64748b]">Nv. ${pokemon.level}</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#cbd5e1]">
                                    <div class="h-full rounded-full ${hpPercent > 35 ? 'bg-[#22c55e]' : 'bg-[#ef4444]'}" style="width: ${hpPercent}%"></div>
                                </div>
                                <p class="mt-1 text-[11px] font-bold text-[#64748b]">${pokemon.hp}/${pokemon.maxHp} HP</p>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            ${typePills(pokemon.types)}
                            ${isActive ? '<span class="rounded-lg bg-[#facc15] px-2 py-1 text-[10px] font-black uppercase text-[#0f172a]">Ativo</span>' : `<button type="button" data-active="${index}" class="active-btn rounded-lg bg-[#0f172a] px-2 py-1 text-[10px] font-black uppercase text-white">Usar</button>`}
                            ${pokemon.level >= 20 ? '<span class="rounded-lg bg-[#14b8a6] px-2 py-1 text-[10px] font-black uppercase text-white">Forma alt.</span>' : ''}
                            ${pokemon.level >= 30 ? '<span class="rounded-lg bg-[#ef4444] px-2 py-1 text-[10px] font-black uppercase text-white">Mega</span>' : ''}
                            ${pokemon.level >= 45 ? '<span class="rounded-lg bg-[#8b5cf6] px-2 py-1 text-[10px] font-black uppercase text-white">G-Max</span>' : ''}
                        </div>
                    </div>
                `;
            }).join('');

            partyList.querySelectorAll('.active-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    state.activeIndex = Number(button.dataset.active);
                    logMessage(`${state.party[state.activeIndex].name} agora e o Pokemon ativo.`);
                    renderAll();
                    saveGame();
                });
            });
        }

        function renderLog() {
            const entries = state.log.slice(-12).reverse();
            gameLog.innerHTML = entries.length
                ? entries.map((entry) => `<p>${entry}</p>`).join('')
                : '<p>O registro da aventura aparece aqui.</p>';
        }

        function renderBattle() {
            if (!battle) {
                return;
            }

            const active = getActivePokemon();
            const wild = battle.wild;
            document.getElementById('wild-name').textContent = `${displayMonName(wild)} Nv. ${wild.level}`;
            document.getElementById('wild-types').innerHTML = typePills(wild.types);
            document.getElementById('wild-sprite').src = wild.sprite;
            document.getElementById('wild-hp-text').textContent = `${wild.hp}/${wild.maxHp}`;
            document.getElementById('wild-hp-bar').style.width = `${hpPercentValue(wild)}%`;

            document.getElementById('active-name').textContent = `${displayMonName(active)} Nv. ${active.level}`;
            document.getElementById('active-types').innerHTML = typePills(active.types);
            document.getElementById('active-sprite').src = active.sprite;
            document.getElementById('active-hp-text').textContent = `${active.hp}/${active.maxHp}`;
            document.getElementById('active-hp-bar').style.width = `${hpPercentValue(active)}%`;

            const moveButtons = document.getElementById('move-buttons');
            moveButtons.innerHTML = active.moves.map((move, index) => `
                <button type="button" data-move="${index}" class="move-action rounded-lg px-4 py-3 text-left text-sm font-black uppercase text-white"
                    style="background-color: ${TYPE_COLORS[move.type] || '#0f172a'}">
                    ${move.name}
                    <span class="block text-[10px] font-bold opacity-80">${TYPE_LABELS[move.type] || titleCase(move.type)} | Poder ${move.power || '-'}</span>
                </button>
            `).join('');

            moveButtons.querySelectorAll('.move-action').forEach((button) => {
                button.addEventListener('click', () => playerAttack(Number(button.dataset.move)));
            });
        }

        async function playerAttack(moveIndex) {
            if (!battle || battle.locked) {
                return;
            }

            battle.locked = true;
            const active = getActivePokemon();
            const move = active.moves[moveIndex] || active.moves[0];
            await resolveAttack(active, battle.wild, move, true);

            if (battle.wild.hp <= 0) {
                await winBattle();
                return;
            }

            await wait(650);
            await wildTurn();
        }

        async function wildTurn() {
            if (!battle) {
                return;
            }

            const active = getActivePokemon();
            const wildMove = battle.wild.moves[Math.floor(Math.random() * battle.wild.moves.length)];
            await resolveAttack(battle.wild, active, wildMove, false);

            if (active.hp <= 0) {
                logMessage(`${active.name} desmaiou.`);
                const next = state.party.findIndex((pokemon) => pokemon.hp > 0);
                if (next >= 0) {
                    state.activeIndex = next;
                    logMessage(`${state.party[next].name} entrou na batalha.`);
                    battle.locked = false;
                    renderAll();
                    renderBattle();
                    saveGame();
                    return;
                }

                logMessage('Sua equipe foi derrotada. Voce voltou ao Centro Pokemon.');
                healParty();
                closeBattle();
                return;
            }

            battle.locked = false;
            renderAll();
            renderBattle();
            saveGame();
        }

        async function resolveAttack(attacker, defender, move, byPlayer) {
            if (!move) {
                move = { name: 'Investida', type: 'normal', power: 40, accuracy: 100 };
            }

            if (move.status === 'focus') {
                attacker.tempForm = attacker.tempForm || 'focus';
                logMessage(`${attacker.name} concentrou energia.`);
                renderBattle();
                return;
            }

            if (Math.random() * 100 > (move.accuracy ?? 100)) {
                logMessage(`${attacker.name} errou ${move.name}.`);
                renderBattle();
                return;
            }

            const stab = attacker.types.includes(move.type) ? 1.25 : 1;
            const random = Phaser.Math.FloatBetween(0.86, 1);
            const attack = currentStat(attacker, 'attack');
            const defense = Math.max(1, currentStat(defender, 'defense'));
            const damage = Math.max(1, Math.round((((attacker.level * 0.42 + 2) * (move.power || 35) * (attack / defense)) / 7) * stab * random));
            defender.hp = Math.max(0, defender.hp - damage);
            logMessage(`${attacker.name} usou ${move.name} e causou ${damage} de dano.`);
            renderAll();
            renderBattle();
            await wait(byPlayer ? 350 : 500);
        }

        async function captureWild() {
            if (!battle || battle.locked) {
                return;
            }

            battle.locked = true;
            const chance = captureChance(battle.wild);
            logMessage(`Tentativa de captura: ${Math.round(chance * 100)}% de chance.`);
            await wait(500);

            if (Math.random() < chance) {
                const captured = captureClone(battle.wild);
                if (state.party.length < 6) {
                    state.party.push(captured);
                    logMessage(`${captured.name} foi capturado e entrou na equipe.`);
                } else {
                    state.box.push(captured);
                    logMessage(`${captured.name} foi capturado e enviado para o box.`);
                }
                state.caught += 1;
                closeBattle();
                renderAll();
                saveGame();
                return;
            }

            logMessage(`${battle.wild.name} escapou da captura.`);
            await wait(500);
            await wildTurn();
        }

        function captureChance(wild) {
            const hpMissing = 1 - (wild.hp / wild.maxHp);
            const active = getActivePokemon();
            const levelGap = Math.max(-6, active.level - wild.level);
            return Math.max(0.12, Math.min(0.88, 0.24 + hpMissing * 0.48 + levelGap * 0.035 + (wild.isCustom ? -0.05 : 0)));
        }

        async function runFromBattle() {
            if (!battle || battle.locked) {
                return;
            }

            battle.locked = true;
            const active = getActivePokemon();
            const chance = currentStat(active, 'speed') >= currentStat(battle.wild, 'speed') ? 0.84 : 0.62;
            if (Math.random() < chance) {
                logMessage('Voce fugiu da batalha.');
                closeBattle();
                return;
            }

            logMessage('A fuga falhou.');
            await wait(400);
            await wildTurn();
        }

        async function winBattle() {
            const active = getActivePokemon();
            const wild = battle.wild;
            const exp = Math.round(wild.level * 18 + wild.maxHp * 0.35);
            logMessage(`${wild.name} foi derrotado. ${active.name} ganhou ${exp} EXP.`);
            await gainExp(active, exp);
            closeBattle();
            renderAll();
            saveGame();
        }

        async function gainExp(pokemon, amount) {
            pokemon.exp += amount;
            while (pokemon.exp >= expToNext(pokemon)) {
                pokemon.exp -= expToNext(pokemon);
                pokemon.level += 1;
                pokemon.maxHp += Phaser.Math.Between(4, 7);
                pokemon.hp = pokemon.maxHp;
                pokemon.attack += Phaser.Math.Between(2, 4);
                pokemon.defense += Phaser.Math.Between(1, 3);
                pokemon.speed += Phaser.Math.Between(1, 3);
                logMessage(`${pokemon.name} subiu para o nivel ${pokemon.level}.`);
                queueLearnMoves(pokemon);
            }
            showNextLearnPrompt();
        }

        function expToNext(pokemon) {
            return 35 + pokemon.level * 24;
        }

        function queueLearnMoves(pokemon) {
            const learnset = learnsetFor(pokemon);
            learnset
                .filter((move) => move.level === pokemon.level && !pokemon.learned.includes(moveKey(move)))
                .forEach((move) => pendingLearn.push({ pokemonUuid: pokemon.uuid, move }));
        }

        function learnsetFor(pokemon) {
            const moves = [...UNIVERSAL_MOVES];
            pokemon.types.forEach((type) => {
                moves.push(...(TYPE_MOVES[type] || []));
            });
            return moves.sort((a, b) => a.level - b.level);
        }

        function showNextLearnPrompt() {
            if (learnModalOpen || pendingLearn.length === 0) {
                return;
            }

            const item = pendingLearn[0];
            const pokemon = findPokemonByUuid(item.pokemonUuid);
            if (!pokemon) {
                pendingLearn.shift();
                showNextLearnPrompt();
                return;
            }

            learnModalOpen = true;
            document.getElementById('learn-title').textContent = `${pokemon.name} quer aprender ${item.move.name}`;
            document.getElementById('learn-description').textContent = `${item.move.name} e um golpe do tipo ${TYPE_LABELS[item.move.type] || titleCase(item.move.type)} com poder ${item.move.power || 'especial'}. Deseja usar esta habilidade no Pokemon?`;

            const replaceWrapper = document.getElementById('replace-wrapper');
            const replaceMove = document.getElementById('replace-move');
            if (pokemon.moves.length >= 4) {
                replaceWrapper.classList.remove('hidden');
                replaceMove.innerHTML = pokemon.moves.map((move, index) => `<option value="${index}">${move.name}</option>`).join('');
            } else {
                replaceWrapper.classList.add('hidden');
                replaceMove.innerHTML = '';
            }

            learnModal.classList.remove('hidden');
            learnModal.classList.add('flex');
        }

        function acceptLearnMove() {
            const item = pendingLearn.shift();
            const pokemon = findPokemonByUuid(item.pokemonUuid);
            if (pokemon) {
                const move = { ...item.move };
                delete move.level;
                if (pokemon.moves.length >= 4) {
                    const replaceIndex = Number(document.getElementById('replace-move').value || 0);
                    const oldMove = pokemon.moves[replaceIndex];
                    pokemon.moves[replaceIndex] = move;
                    logMessage(`${pokemon.name} esqueceu ${oldMove.name} e aprendeu ${move.name}.`);
                } else {
                    pokemon.moves.push(move);
                    logMessage(`${pokemon.name} aprendeu ${move.name}.`);
                }
                pokemon.learned.push(moveKey(move));
            }
            closeLearnPrompt();
        }

        function skipLearnMove() {
            const item = pendingLearn.shift();
            const pokemon = findPokemonByUuid(item.pokemonUuid);
            if (pokemon) {
                pokemon.learned.push(moveKey(item.move));
                logMessage(`${pokemon.name} nao aprendeu ${item.move.name}.`);
            }
            closeLearnPrompt();
        }

        function closeLearnPrompt() {
            learnModalOpen = false;
            learnModal.classList.add('hidden');
            learnModal.classList.remove('flex');
            renderAll();
            saveGame();
            showNextLearnPrompt();
        }

        function applyBattleForm(form) {
            if (!battle || battle.locked) {
                return;
            }

            const pokemon = getActivePokemon();

            if (form === 'mega') {
                if (pokemon.level < 30) {
                    logMessage('Mega evolucao desbloqueia no nivel 30.');
                    return;
                }
                if (battle.usedMega) {
                    logMessage('Mega evolucao ja foi usada nesta batalha.');
                    return;
                }
                pokemon.tempForm = 'mega';
                battle.usedMega = true;
                logMessage(`${pokemon.name} mega evoluiu nesta batalha.`);
            }

            if (form === 'gmax') {
                if (pokemon.level < 45) {
                    logMessage('Gigantamax desbloqueia no nivel 45.');
                    return;
                }
                if (battle.usedGmax) {
                    logMessage('Gigantamax ja foi usado nesta batalha.');
                    return;
                }
                pokemon.tempForm = 'gmax';
                battle.usedGmax = true;
                logMessage(`${pokemon.name} entrou na forma Gigantamax.`);
            }

            if (form === 'alt') {
                if (pokemon.level < 20) {
                    logMessage('Forma alternativa desbloqueia no nivel 20.');
                    return;
                }
                pokemon.altForm = !pokemon.altForm;
                logMessage(`${pokemon.name} ${pokemon.altForm ? 'assumiu' : 'deixou'} a forma alternativa.`);
            }

            renderAll();
            renderBattle();
            saveGame();
        }

        function closeBattle() {
            if (battle) {
                cleanupBattleForms();
            }
            battle = null;
            battleModal.classList.add('hidden');
            battleModal.classList.remove('flex');
            renderAll();
            saveGame();
        }

        function cleanupBattleForms() {
            state.party.forEach((pokemon) => {
                if (pokemon.tempForm === 'mega' || pokemon.tempForm === 'gmax' || pokemon.tempForm === 'focus') {
                    pokemon.tempForm = null;
                }
            });
        }

        function healParty() {
            state.party.forEach((pokemon) => {
                pokemon.hp = pokemon.maxHp;
                pokemon.tempForm = null;
            });
            renderAll();
            if (battle) {
                renderBattle();
            }
            saveGame();
        }

        function captureClone(pokemon) {
            return {
                ...pokemon,
                uuid: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
                owned: true,
                hp: pokemon.maxHp,
                tempForm: null
            };
        }

        function findPokemonByUuid(uuid) {
            return [...state.party, ...state.box].find((pokemon) => pokemon.uuid === uuid);
        }

        function currentStat(pokemon, stat) {
            const formMultiplier = pokemon.tempForm === 'gmax' ? 1.55 : (pokemon.tempForm === 'mega' ? 1.35 : (pokemon.tempForm === 'focus' ? 1.12 : 1));
            const altMultiplier = pokemon.altForm ? 1.08 : 1;
            return Math.round((pokemon[stat] || 1) * formMultiplier * altMultiplier);
        }

        function displayMonName(pokemon) {
            const prefix = pokemon.tempForm === 'mega' ? 'Mega ' : (pokemon.tempForm === 'gmax' ? 'G-Max ' : '');
            const suffix = pokemon.altForm ? ' Forma Alt.' : '';
            return `${prefix}${pokemon.name}${suffix}`;
        }

        function hpPercentValue(pokemon) {
            return Math.max(0, Math.min(100, Math.round((pokemon.hp / pokemon.maxHp) * 100)));
        }

        function typePills(types) {
            return (types || ['normal']).map((type) => {
                const color = TYPE_COLORS[type] || '#0f172a';
                const text = ['electric', 'ice', 'grass', 'ground', 'flying', 'steel', 'fairy', 'normal'].includes(type) ? '#0f172a' : '#ffffff';
                return `<span class="rounded-lg px-2 py-1 text-[10px] font-black uppercase" style="background:${color};color:${text}">${TYPE_LABELS[type] || titleCase(type)}</span>`;
            }).join('');
        }

        function moveKey(move) {
            return `${move.name}-${move.type}`.toLowerCase();
        }

        function titleCase(value) {
            return String(value || '')
                .replaceAll('-', ' ')
                .split(' ')
                .filter(Boolean)
                .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
                .join(' ');
        }

        function placeholderImage(name) {
            return `https://placehold.co/160x160/0f172a/facc15?text=${encodeURIComponent(name || 'Pokemon')}`;
        }

        function logMessage(message) {
            state.log.push(message);
            state.log = state.log.slice(-40);
            renderLog();
            saveGame();
        }

        function wait(ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        }
    </script>
</body>

</html>
