<?php

namespace App\Repositories;

use App\Services\SupabaseClient;

class CompanyRepository
{
    private SupabaseClient $client;

    public function __construct()
    {
        $this->client = new SupabaseClient();
    }

    public function all()
    {
        return $this->client->select('companies');
    }

    public function findById(string $id)
    {
        return $this->client->select('companies', [
            'id' => "eq.$id"
        ]);
    }

    public function create(array $data)
    {
        return $this->client->insert('companies', [$data]);
    }

    public function update(string $id, array $data)
    {
        return $this->client->update('companies', [$data], "id=eq.$id");
    }

    public function delete(string $id)
    {
        return $this->client->delete('companies', "id=eq.$id");
    }
}
