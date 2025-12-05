<?php

namespace App\Repositories;

use App\Services\SupabaseClient;

class UserRepository
{
    private SupabaseClient $client;

    public function __construct()
    {
        $this->client = new SupabaseClient();
    }

    public function all()
    {
        return $this->client->select('users');
    }

    public function findById(string $id)
    {
        return $this->client->select('users', [
            'id' => "eq.$id"
        ]);
    }

    public function create(array $data)
    {
        return $this->client->insert('users', [$data]);
    }

    public function update(string $id, array $data)
    {
        return $this->client->update('users', [$data], "id=eq.$id");
    }

    public function delete(string $id)
    {
        return $this->client->delete('users', "id=eq.$id");
    }
}
