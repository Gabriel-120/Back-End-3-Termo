<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\SamplePokemonSeeder;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pokemon:reset-samples', function (SamplePokemonSeeder $seeder) {
    $result = $seeder->resetAndSeed();

    $this->info("Pokemon personalizados apagados: {$result['deleted']}");
    $this->info('Pokemon de exemplo criados:');

    foreach ($result['created'] as $pokemon) {
        $this->line('- '.$pokemon);
    }
})->purpose('Apaga Pokemon criados/fundidos e cria os exemplos solicitados');
