<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pokemons')) {
            Schema::create('pokemons', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('pokedex_number')->unique();
                $table->string('name');
                $table->string('slug');
                $table->string('source')->default('manual');
                $table->string('generation')->default('custom');
                $table->string('rarity')->nullable();
                $table->string('image_path')->nullable();
                $table->string('image_url')->nullable();
                $table->decimal('height', 8, 2)->default(0);
                $table->decimal('weight', 8, 2)->default(0);
                $table->unsignedInteger('base_experience')->default(0);
                $table->text('description')->nullable();
                $table->json('types')->nullable();
                $table->json('abilities')->nullable();
                $table->json('stats')->nullable();
                $table->json('moves')->nullable();
                $table->json('evolutions')->nullable();
                $table->json('variants')->nullable();
                $table->json('locations')->nullable();
                $table->json('meta')->nullable();
                $table->json('fusion')->nullable();
                $table->json('ai_payload')->nullable();
                $table->timestamps();

                $table->index(['source', 'generation']);
                $table->index('rarity');
            });
        }

        $this->importLegacyJson();
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }

    private function importLegacyJson(): void
    {
        $path = storage_path('app/custom_pokemon.json');

        if (! file_exists($path)) {
            return;
        }

        $records = json_decode(file_get_contents($path) ?: '[]', true);

        if (! is_array($records)) {
            return;
        }

        foreach ($records as $record) {
            if (empty($record['pokedex_number']) || empty($record['name'])) {
                continue;
            }

            DB::table('pokemons')->updateOrInsert(
                ['pokedex_number' => (int) $record['pokedex_number']],
                [
                    'name' => $record['name'],
                    'slug' => $record['slug'] ?? Str::slug($record['name']),
                    'source' => $record['source'] ?? 'manual',
                    'generation' => $record['generation'] ?? 'custom',
                    'rarity' => $record['rarity'] ?? data_get($record, 'meta.rarity'),
                    'image_path' => $record['image_path'] ?? null,
                    'image_url' => $record['image_url'] ?? null,
                    'height' => (float) ($record['height'] ?? 0),
                    'weight' => (float) ($record['weight'] ?? 0),
                    'base_experience' => (int) ($record['base_experience'] ?? 0),
                    'description' => $record['description'] ?? null,
                    'types' => json_encode($record['types'] ?? []),
                    'abilities' => json_encode($record['abilities'] ?? []),
                    'stats' => json_encode($record['stats'] ?? []),
                    'moves' => json_encode($record['moves'] ?? []),
                    'evolutions' => json_encode($record['evolutions'] ?? []),
                    'variants' => json_encode($record['variants'] ?? []),
                    'locations' => json_encode($record['locations'] ?? []),
                    'meta' => json_encode($record['meta'] ?? []),
                    'fusion' => json_encode($record['fusion'] ?? []),
                    'ai_payload' => json_encode($record['ai_payload'] ?? data_get($record, 'meta.ai_payload', [])),
                    'created_at' => $this->mysqlDate($record['created_at'] ?? null),
                    'updated_at' => $this->mysqlDate($record['updated_at'] ?? null),
                ]
            );
        }
    }

    private function mysqlDate(?string $value): string
    {
        if (! $value) {
            return now()->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return now()->format('Y-m-d H:i:s');
        }
    }
};
