<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MateriaLegislativa;
use App\Models\Parlamentar;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class ContarAcesso
{
    /**
     * Manipula a requisição.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Realiza tarefas após a resposta ser enviada ao navegador.
     * Utiliza Cache Lock para evitar contagem duplicada (+2).
     */
    public function terminate(Request $request, Response $response): void
    {
        // 1. Verificações iniciais (Apenas GET, ignora Turbo/Prefetch)
        if (! $request->isMethod('get') || 
            $request->headers->get('Turbo-Frame') || 
            $request->headers->has('X-Sec-Purpose', 'prefetch')) {
            return;
        }

        // --- SISTEMA ANTI-DUPLICAÇÃO (DEBOUNCE) ---
        // Cria uma chave única baseada no IP e na URL acessada
        $cacheKey = 'view_lock_' . md5($request->ip() . $request->fullUrl());

        // Tenta adicionar essa chave no cache por 5 segundos.
        // O método 'add' retorna true se conseguir gravar, e false se a chave já existir.
        // Se retornar false, significa que essa requisição aconteceu há menos de 5 segundos.
        if (! Cache::add($cacheKey, true, 5)) {
            return; 
        }

        // 2. Contagem para Matérias
        if ($request->routeIs('materias.show')) {
            $materiaId = $request->route('id');
            // Incremento atômico (direto no banco)
            MateriaLegislativa::where('id', $materiaId)->increment('acessos');
        }

        // 3. Contagem para Parlamentar
        if ($request->routeIs('parlamentar.show')) {
            $slug = $request->route('parlamentar');

            // Busca e incrementa usando a lógica de string do banco
            Parlamentar::whereRaw("LOWER(REPLACE(nome_parlamentar, ' ', '-')) = ?", [strtolower($slug)])
                ->increment('acessos');
        }
    }
}