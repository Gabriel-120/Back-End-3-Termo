<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->query('id', rand(1, 100));
        $id = is_numeric($id) ? (int) $id : rand(1, 100);
        $id = max(1, min(100, $id));

        $response = Http::get("https://dummyjson.com/users/{$id}");

        if (! $response->successful()) {
            return "Erro ao buscar dados do usuário";
        }

        $user = $response->json();
        $user = array_merge([
            'firstName' => '',
            'lastName' => '',
            'username' => '',
            'age' => null,
            'gender' => '',
            'email' => '',
            'phone' => '',
            'image' => 'https://via.placeholder.com/300x300?text=Sem+imagem',
            'address' => [
                'address' => 'Não disponível',
                'city' => 'Não disponível',
                'state' => 'Não disponível',
                'postalCode' => 'Não disponível',
            ],
            'company' => [
                'name' => 'Não disponível',
                'department' => 'Não disponível',
                'title' => 'Não disponível',
            ],
            'university' => 'Não disponível',
            'bloodGroup' => 'Não disponível',
            'ip' => 'Não disponível',
            'domain' => 'Não disponível',
            'weight' => '—',
            'height' => '—',
        ], $user);

        $user['address'] = array_merge([
            'address' => 'Não disponível',
            'city' => 'Não disponível',
            'state' => 'Não disponível',
            'postalCode' => 'Não disponível',
        ], $user['address'] ?? []);

        $user['company'] = array_merge([
            'name' => 'Não disponível',
            'department' => 'Não disponível',
            'title' => 'Não disponível',
        ], $user['company'] ?? []);

        $user['fullName'] = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));

        return view('usuario', compact('user'));
    }

    public function search(Request $request)
    {
        $query = trim($request->query('q', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $queryLower = strtolower($query);
        $response = Http::get('https://dummyjson.com/users', [
            'limit' => 100,
        ]);

        if (! $response->successful()) {
            return response()->json([]);
        }

        $users = $response->json()['users'] ?? [];
        $results = [];

        foreach ($users as $user) {
            if (count($results) >= 10) {
                break;
            }

            $name = strtolower(trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? '')));
            $username = strtolower($user['username'] ?? '');
            $id = $user['id'] ?? null;

            if (is_numeric($query) && strpos((string) $id, $queryLower) === 0) {
                $results[] = [
                    'id' => $id,
                    'name' => ucfirst($name),
                ];
                continue;
            }

            if (strpos($name, $queryLower) !== false || strpos($username, $queryLower) !== false) {
                $results[] = [
                    'id' => $id,
                    'name' => ucfirst($name),
                ];
            }
        }

        return response()->json($results);
    }
}
