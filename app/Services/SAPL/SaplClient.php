<?php

namespace App\Services\Sapl;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Cliente HTTP centralizado para integração com a API do SAPL.
 *
 * Responsável por realizar requisições GET à API, com tratamento de erros,
 * retry automático e timeouts configurados para evitar bloqueios prolongados.
 */
class SaplClient
{
    /** URL base da API SAPL (ex: https://sapl.exemplo.leg.br/api) */
    protected string $baseApiUrl;

    /** URL base pública do SAPL (ex: https://sapl.exemplo.leg.br) – usada para links clicáveis */
    protected string $basePublicUrl;

    /**
     * Construtor.
     *
     * Normaliza a URL base e deriva a versão pública removendo o sufixo '/api'.
     *
     * @param string $baseApiUrl URL completa da API SAPL
     */
    public function __construct(string $baseApiUrl)
    {
        $this->baseApiUrl = rtrim($baseApiUrl, '/');

        // Remove '/api' do final para obter a URL pública
        $this->basePublicUrl = preg_replace('#/api$#i', '', $this->baseApiUrl);
    }

    /**
     * Executa uma requisição GET à API SAPL.
     *
     * Configurações aplicadas:
     * - Timeout total de 30 segundos (evita esperas infinitas).
     * - Timeout de conexão de 10 segundos.
     * - Retry automático: 2 tentativas com delay de 300ms.
     *
     * Em caso de falha (timeout, conexão, exceção), retorna array vazio de forma graceful.
     *
     * @param string $endpoint Endpoint relativo (ex: '/materia/materialegislativa/')
     * @param array  $params   Parâmetros query string
     *
     * @return array Resposta JSON decodificada ou array vazio em falha
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            return Http::timeout(30)
                ->connectTimeout(10)
                ->retry(2, 300)
                ->get($this->baseApiUrl . $endpoint, $params)
                ->throw() // Lança exceção em respostas não-succeso (4xx/5xx)
                ->json() ?? [];
        } catch (Throwable) {
            // Log opcional pode ser adicionado aqui em produção
            return [];
        }
    }

    /**
     * Retorna a URL base da API (útil para cache e debug).
     *
     * @return string
     */
    public function getBaseApiUrl(): string
    {
        return $this->baseApiUrl;
    }

    /**
     * Retorna a URL base pública do SAPL (usada para construção de links clicáveis).
     *
     * @return string
     */
    public function getBasePublicUrl(): string
    {
        return $this->basePublicUrl;
    }
}