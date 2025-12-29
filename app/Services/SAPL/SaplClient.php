<?php

namespace App\Services\Sapl;

use Illuminate\Support\Facades\Http;
use Throwable;

class SaplClient
{
    protected string $baseApiUrl;
    protected string $basePublicUrl;

    public function __construct(string $baseApiUrl)
    {
        $this->baseApiUrl = rtrim($baseApiUrl, '/');

        // https://sapl.xxx.leg.br/api  →  https://sapl.xxx.leg.br
        $this->basePublicUrl = preg_replace('#/api$#', '', $this->baseApiUrl);
    }

    public function get(string $endpoint, array $params = []): array
    {
        try {
            return Http::timeout(8)
                ->retry(2, 300)
                ->get($this->baseApiUrl . $endpoint, $params)
                ->json() ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * URL base da API (cache, debug, etc.)
     */
    public function getBaseApiUrl(): string
    {
        return $this->baseApiUrl;
    }

    /**
     * URL pública do SAPL (links clicáveis)
     */
    public function getBasePublicUrl(): string
    {
        return $this->basePublicUrl;
    }
}
