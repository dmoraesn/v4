<?php

namespace App\Services\Sapl;

class SaplAutoresService
{
    protected SaplClient $client;

    public function __construct(string $saplUrl)
    {
        $this->client = new SaplClient($saplUrl);
    }

    public function listarAutores(): array
    {
        $data = $this->client->get('/api/base/parlamentares/');

        if (!$data || !isset($data['results'])) {
            return [];
        }

        return collect($data['results'])->map(function ($autor) {
            return [
                'id'   => $autor['id'],
                'nome' => $autor['nome_parlamentar'],
                'ativo'=> $autor['ativo'],
                'url'  => $autor['id']
                    ? url()->current() . '/' . $autor['id']
                    : null,
            ];
        })->toArray();
    }
}
