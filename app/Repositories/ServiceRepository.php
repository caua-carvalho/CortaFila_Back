<?php

namespace App\Repositories;

use App\Services\SupabaseClient;

class ServiceRepository
{
    private SupabaseClient $client;

    public function __construct()
    {
        $this->client = new SupabaseClient();
    }

    public function all()
    {
        return $this->client->select('services');
    }

    public function findById(string $id)
    {
        return $this->client->select('services', [
            'id' => "eq.$id"
        ]);
    }

    public function create(array $data)
    {
        return $this->client->insert('services', [$data]);
    }

    public function update(string $id, array $data)
    {
        return $this->client->update('services', [$data], "id=eq.$id");
    }

    public function delete(string $id)
    {
        return $this->client->delete('services', "id=eq.$id");
    }
}
