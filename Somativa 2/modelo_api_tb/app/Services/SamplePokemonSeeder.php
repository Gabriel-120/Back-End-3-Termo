<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SamplePokemonSeeder
{
    public function resetAndSeed(): array
    {
        $deleted = $this->deleteAllCustomPokemon();
        $created = $this->seedSamples();

        return [
            'deleted' => $deleted,
            'created' => $created,
        ];
    }

    public function deleteAllCustomPokemon(): int
    {
        $records = Pokemon::query()->get(['image_path']);

        foreach ($records as $record) {
            if (! empty($record->image_path)) {
                Storage::disk('public')->delete($record->image_path);
            }
        }

        Storage::disk('public')->deleteDirectory('pokemon-images/samples');

        return Pokemon::query()->delete();
    }

    public function seedSamples(): array
    {
        $records = [
            [
                'id' => 10001,
                'name' => 'Aerignis',
                'family' => 'skyflame',
                'stage' => 1,
                'types' => ['fire', 'dragon'],
                'rarity' => 'rare',
                'height' => 0.7,
                'weight' => 9.4,
                'base_experience' => 68,
                'description' => 'Uma ave jovem com penas aquecidas e pequenas escamas de dragao nas asas. Ela acende faiscas quando bate as asas.',
                'abilities' => ['flame-body', 'keen-eye'],
                'stats' => ['hp' => 48, 'attack' => 57, 'defense' => 43, 'special-attack' => 68, 'special-defense' => 48, 'speed' => 72],
                'moves' => [
                    ['name' => 'Asa Incandescente', 'type' => 'fire', 'category' => 'special', 'power' => 55, 'accuracy' => 100, 'pp' => 25, 'effect' => 'Ataca com penas aquecidas por energia draconica.'],
                    ['name' => 'Sopro de Brasa', 'type' => 'dragon', 'category' => 'special', 'power' => 50, 'accuracy' => 95, 'pp' => 20, 'effect' => 'Sopra uma chama fina com energia de dragao.'],
                ],
                'meta' => ['color' => 'red', 'habitat' => 'montanhas quentes', 'shape' => 'bird', 'growth_rate' => 'medium'],
            ],
            [
                'id' => 10002,
                'name' => 'Pyravis',
                'family' => 'skyflame',
                'stage' => 2,
                'types' => ['fire', 'dragon'],
                'rarity' => 'epic',
                'height' => 1.4,
                'weight' => 27.8,
                'base_experience' => 142,
                'description' => 'A evolucao de Aerignis. Suas penas viram laminas flamejantes e o bater das asas cria correntes de calor.',
                'abilities' => ['flame-body', 'dragon-aura'],
                'stats' => ['hp' => 67, 'attack' => 78, 'defense' => 61, 'special-attack' => 91, 'special-defense' => 67, 'speed' => 96],
                'moves' => [
                    ['name' => 'Rajada Rubra', 'type' => 'fire', 'category' => 'special', 'power' => 75, 'accuracy' => 95, 'pp' => 15, 'effect' => 'Lanca uma rajada de vento e fogo.'],
                    ['name' => 'Garra Draconica', 'type' => 'dragon', 'category' => 'physical', 'power' => 80, 'accuracy' => 100, 'pp' => 15, 'effect' => 'Corta o alvo com garras carregadas de energia draconica.'],
                ],
                'meta' => ['color' => 'orange', 'habitat' => 'picos vulcanicos', 'shape' => 'bird', 'growth_rate' => 'medium'],
            ],
            [
                'id' => 10003,
                'name' => 'Dracofenix',
                'family' => 'skyflame',
                'stage' => 3,
                'types' => ['fire', 'dragon'],
                'rarity' => 'legendary',
                'height' => 2.3,
                'weight' => 74.5,
                'base_experience' => 246,
                'description' => 'Forma final da linhagem. Dracofenix domina os ceus deixando rastros de fogo azul e rugidos que lembram dragoes antigos.',
                'abilities' => ['flame-body', 'phoenix-heart'],
                'stats' => ['hp' => 88, 'attack' => 105, 'defense' => 82, 'special-attack' => 124, 'special-defense' => 90, 'speed' => 118],
                'moves' => [
                    ['name' => 'Cometa Flamejante', 'type' => 'fire', 'category' => 'special', 'power' => 110, 'accuracy' => 90, 'pp' => 5, 'effect' => 'Mergulha como um meteoro envolto em fogo.'],
                    ['name' => 'Rugido Celeste', 'type' => 'dragon', 'category' => 'special', 'power' => 95, 'accuracy' => 95, 'pp' => 10, 'effect' => 'Libera um rugido que vibra com energia de dragao.'],
                ],
                'meta' => ['color' => 'crimson', 'habitat' => 'ceus vulcanicos', 'shape' => 'bird-dragon', 'growth_rate' => 'slow'],
            ],
            [
                'id' => 10004,
                'name' => 'Ferrugro',
                'family' => 'ironogre',
                'stage' => 1,
                'types' => ['fighting', 'steel'],
                'rarity' => 'rare',
                'height' => 0.9,
                'weight' => 42.0,
                'base_experience' => 70,
                'description' => 'Um pequeno ogro de metal que treina socando rochas. Seu corpo fica mais resistente a cada batalha.',
                'abilities' => ['iron-fist', 'sturdy'],
                'stats' => ['hp' => 56, 'attack' => 78, 'defense' => 74, 'special-attack' => 32, 'special-defense' => 48, 'speed' => 38],
                'moves' => [
                    ['name' => 'Soco Rebitado', 'type' => 'steel', 'category' => 'physical', 'power' => 60, 'accuracy' => 100, 'pp' => 20, 'effect' => 'Golpeia com punhos metalicos.'],
                    ['name' => 'Quebra Guarda', 'type' => 'fighting', 'category' => 'physical', 'power' => 55, 'accuracy' => 100, 'pp' => 25, 'effect' => 'Ataca com postura de luta pesada.'],
                ],
                'meta' => ['color' => 'gray', 'habitat' => 'minas antigas', 'shape' => 'humanoid', 'growth_rate' => 'medium'],
            ],
            [
                'id' => 10005,
                'name' => 'Martelogro',
                'family' => 'ironogre',
                'stage' => 2,
                'types' => ['fighting', 'steel'],
                'rarity' => 'epic',
                'height' => 1.7,
                'weight' => 118.0,
                'base_experience' => 148,
                'description' => 'A evolucao de Ferrugro. Seus bracos parecem martelos de ferro e abrem crateras no chao durante o treino.',
                'abilities' => ['iron-fist', 'battle-armor'],
                'stats' => ['hp' => 78, 'attack' => 108, 'defense' => 101, 'special-attack' => 42, 'special-defense' => 67, 'speed' => 49],
                'moves' => [
                    ['name' => 'Martelo Marcial', 'type' => 'fighting', 'category' => 'physical', 'power' => 85, 'accuracy' => 95, 'pp' => 15, 'effect' => 'Desfere um golpe pesado de tecnica e forca bruta.'],
                    ['name' => 'Blindagem Viva', 'type' => 'steel', 'category' => 'status', 'power' => 0, 'accuracy' => 100, 'pp' => 20, 'effect' => 'Endurece o corpo metalico e aumenta a defesa.'],
                ],
                'meta' => ['color' => 'steel', 'habitat' => 'forjas subterraneas', 'shape' => 'humanoid', 'growth_rate' => 'medium'],
            ],
            [
                'id' => 10006,
                'name' => 'Titangro',
                'family' => 'ironogre',
                'stage' => 3,
                'types' => ['fighting', 'steel'],
                'rarity' => 'legendary',
                'height' => 2.6,
                'weight' => 286.0,
                'base_experience' => 248,
                'description' => 'Forma final da linhagem. Titangro luta como um campeao antigo e usa chifres de ferro para proteger aliados.',
                'abilities' => ['iron-fist', 'titan-guard'],
                'stats' => ['hp' => 104, 'attack' => 138, 'defense' => 126, 'special-attack' => 55, 'special-defense' => 89, 'speed' => 62],
                'moves' => [
                    ['name' => 'Punho Colossal', 'type' => 'fighting', 'category' => 'physical', 'power' => 115, 'accuracy' => 90, 'pp' => 5, 'effect' => 'Concentra toda a massa do corpo em um soco devastador.'],
                    ['name' => 'Chifre de Aco', 'type' => 'steel', 'category' => 'physical', 'power' => 100, 'accuracy' => 95, 'pp' => 10, 'effect' => 'Avanca com chifres metalicos endurecidos.'],
                ],
                'meta' => ['color' => 'iron', 'habitat' => 'montanhas metalicas', 'shape' => 'giant-humanoid', 'growth_rate' => 'slow'],
            ],
            [
                'id' => 10007,
                'name' => 'Disfarmino',
                'family' => 'maskshade',
                'stage' => 1,
                'types' => ['ghost', 'dark'],
                'rarity' => 'rare',
                'height' => 0.4,
                'weight' => 2.1,
                'base_experience' => 92,
                'description' => 'Um Pokemon timido que usa uma fantasia costurada para parecer amigavel. A sombra sob o pano se move mesmo quando ele fica parado.',
                'abilities' => ['disguise', 'shadow-tag'],
                'stats' => ['hp' => 52, 'attack' => 76, 'defense' => 58, 'special-attack' => 87, 'special-defense' => 91, 'speed' => 84],
                'moves' => [
                    ['name' => 'Pano Assombrado', 'type' => 'ghost', 'category' => 'special', 'power' => 70, 'accuracy' => 100, 'pp' => 15, 'effect' => 'A fantasia se mexe sozinha e assusta o alvo.'],
                    ['name' => 'Sombra Falsa', 'type' => 'dark', 'category' => 'special', 'power' => 80, 'accuracy' => 95, 'pp' => 10, 'effect' => 'Cria uma sombra que ataca de outro angulo.'],
                ],
                'meta' => ['color' => 'purple', 'habitat' => 'casas abandonadas', 'shape' => 'disguise', 'growth_rate' => 'medium'],
            ],
        ];

        $images = [];
        foreach ($records as $record) {
            $path = 'pokemon-images/samples/'.$record['id'].'-'.Str::slug($record['name']).'.svg';
            Storage::disk('public')->put($path, $this->sampleSvg($record));
            $images[$record['id']] = '/storage/'.$path;
            $records[$this->recordIndex($records, $record['id'])]['image_path'] = $path;
        }

        $families = [];
        foreach ($records as $record) {
            $families[$record['family']][] = [
                'id' => $record['id'],
                'name' => $record['name'],
                'image' => $images[$record['id']],
            ];
        }

        $created = [];
        foreach ($records as $record) {
            Pokemon::query()->updateOrCreate(
                ['pokedex_number' => $record['id']],
                [
                    'name' => $record['name'],
                    'slug' => Str::slug($record['name']),
                    'source' => 'manual',
                    'generation' => 'custom',
                    'rarity' => $record['rarity'],
                    'image_path' => $record['image_path'],
                    'image_url' => null,
                    'height' => $record['height'],
                    'weight' => $record['weight'],
                    'base_experience' => $record['base_experience'],
                    'description' => $record['description'],
                    'types' => $record['types'],
                    'abilities' => $record['abilities'],
                    'stats' => $record['stats'],
                    'moves' => $record['moves'],
                    'evolutions' => $families[$record['family']],
                    'variants' => [],
                    'locations' => [$record['meta']['habitat']],
                    'meta' => array_merge($record['meta'], [
                        'stage' => $record['stage'],
                        'is_legendary' => $record['rarity'] === 'legendary',
                        'is_mythical' => false,
                        'is_baby' => $record['stage'] === 1,
                        'sample_created' => true,
                    ]),
                    'fusion' => [],
                    'ai_payload' => [],
                ]
            );

            $created[] = '#'.$record['id'].' '.$record['name'];
        }

        return $created;
    }

    private function recordIndex(array $records, int $id): int
    {
        foreach ($records as $index => $record) {
            if ((int) $record['id'] === $id) {
                return $index;
            }
        }

        return 0;
    }

    private function sampleSvg(array $record): string
    {
        return match ($record['family']) {
            'skyflame' => $this->skyflameSvg($record),
            'ironogre' => $this->ironOgreSvg($record),
            default => $this->maskShadeSvg($record),
        };
    }

    private function skyflameSvg(array $record): string
    {
        $scale = 0.82 + ($record['stage'] * 0.12);
        $name = $this->xml($record['name']);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320">
  <rect width="320" height="320" rx="34" fill="#1e1b4b"/>
  <circle cx="238" cy="72" r="42" fill="#fb923c" opacity="0.32"/>
  <g transform="translate(160 158) scale({$scale}) translate(-160 -158)">
    <path d="M68 170 C92 88 143 74 160 124 C181 74 232 88 252 170 C217 148 192 154 171 188 C164 200 156 200 149 188 C128 154 103 148 68 170Z" fill="#ef4444"/>
    <path d="M89 170 C111 119 141 109 160 142 C178 109 209 119 231 170 C203 163 182 172 167 204 C162 214 158 214 153 204 C138 172 117 163 89 170Z" fill="#f97316"/>
    <ellipse cx="160" cy="175" rx="43" ry="58" fill="#dc2626"/>
    <path d="M132 122 L160 68 L188 122 C175 113 145 113 132 122Z" fill="#facc15"/>
    <path d="M118 217 C96 239 83 258 78 286 C112 274 132 255 145 229Z" fill="#f97316"/>
    <path d="M202 217 C224 239 237 258 242 286 C208 274 188 255 175 229Z" fill="#f97316"/>
    <circle cx="146" cy="164" r="7" fill="#020617"/>
    <circle cx="174" cy="164" r="7" fill="#020617"/>
    <path d="M160 175 L149 190 H171Z" fill="#fde68a"/>
    <path d="M109 108 C92 76 81 53 86 31 C112 55 132 81 140 113Z" fill="#fb7185"/>
    <path d="M211 108 C228 76 239 53 234 31 C208 55 188 81 180 113Z" fill="#fb7185"/>
  </g>
  <text x="160" y="300" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="700" fill="#fff7ed">{$name}</text>
</svg>
SVG;
    }

    private function ironOgreSvg(array $record): string
    {
        $scale = 0.78 + ($record['stage'] * 0.13);
        $name = $this->xml($record['name']);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320">
  <rect width="320" height="320" rx="34" fill="#111827"/>
  <circle cx="86" cy="84" r="58" fill="#9ca3af" opacity="0.25"/>
  <circle cx="236" cy="92" r="62" fill="#ef4444" opacity="0.2"/>
  <g transform="translate(160 170) scale({$scale}) translate(-160 -170)">
    <path d="M106 126 L78 80 L116 100 L142 68 L160 104 L178 68 L204 100 L242 80 L214 126Z" fill="#e5e7eb"/>
    <rect x="100" y="102" width="120" height="98" rx="34" fill="#6b7280"/>
    <rect x="117" y="132" width="86" height="52" rx="22" fill="#9ca3af"/>
    <circle cx="139" cy="150" r="8" fill="#020617"/>
    <circle cx="181" cy="150" r="8" fill="#020617"/>
    <path d="M140 174 H180" stroke="#111827" stroke-width="8" stroke-linecap="round"/>
    <path d="M94 188 C60 198 52 224 70 246 C98 238 113 218 119 195Z" fill="#cbd5e1"/>
    <path d="M226 188 C260 198 268 224 250 246 C222 238 207 218 201 195Z" fill="#cbd5e1"/>
    <rect x="121" y="198" width="33" height="62" rx="13" fill="#4b5563"/>
    <rect x="166" y="198" width="33" height="62" rx="13" fill="#4b5563"/>
    <path d="M96 110 C82 132 78 150 92 164" stroke="#ef4444" stroke-width="10" stroke-linecap="round"/>
    <path d="M224 110 C238 132 242 150 228 164" stroke="#ef4444" stroke-width="10" stroke-linecap="round"/>
  </g>
  <text x="160" y="300" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="700" fill="#f8fafc">{$name}</text>
</svg>
SVG;
    }

    private function maskShadeSvg(array $record): string
    {
        $name = $this->xml($record['name']);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320">
  <rect width="320" height="320" rx="34" fill="#18181b"/>
  <circle cx="90" cy="78" r="54" fill="#7c3aed" opacity="0.24"/>
  <circle cx="226" cy="92" r="58" fill="#111827" opacity="0.75"/>
  <path d="M100 92 C126 57 193 57 220 92 C238 116 232 223 213 260 C191 243 170 258 160 268 C150 258 129 243 107 260 C88 223 82 116 100 92Z" fill="#f4f4f5"/>
  <path d="M104 109 C126 83 194 83 216 109 C224 141 217 205 205 235 C184 223 173 234 160 244 C147 234 136 223 115 235 C103 205 96 141 104 109Z" fill="#e5e7eb"/>
  <path d="M121 90 L102 46 L145 76Z" fill="#f4f4f5"/>
  <path d="M199 90 L218 46 L175 76Z" fill="#f4f4f5"/>
  <circle cx="140" cy="145" r="12" fill="#111827"/>
  <circle cx="181" cy="145" r="12" fill="#111827"/>
  <circle cx="144" cy="141" r="4" fill="#ffffff"/>
  <circle cx="185" cy="141" r="4" fill="#ffffff"/>
  <path d="M137 190 C153 206 170 206 186 190" fill="none" stroke="#111827" stroke-width="7" stroke-linecap="round"/>
  <path d="M118 216 C142 204 176 227 202 213" fill="none" stroke="#7c3aed" stroke-width="8" stroke-linecap="round" opacity="0.7"/>
  <path d="M82 249 C95 277 123 287 148 271" fill="none" stroke="#020617" stroke-width="18" stroke-linecap="round"/>
  <path d="M238 249 C225 277 197 287 172 271" fill="none" stroke="#020617" stroke-width="18" stroke-linecap="round"/>
  <text x="160" y="300" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="700" fill="#fafafa">{$name}</text>
</svg>
SVG;
    }

    private function xml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
