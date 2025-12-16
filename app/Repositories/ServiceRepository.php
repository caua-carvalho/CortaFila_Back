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

    public function create(array $payload, string $companyId)
    {
        $newService = [
            'type'          => $payload["type"],
            'name'          => $payload['name'],
            'price'         => $payload['price'],
            'duration'      => $payload['duration'],
            'description'   => $payload["description"],
            'company_id'    => $companyId
        ];

        return $this->client->insert('services', [$newService]);
    }

    public function update(string $id, string $companyId, array $payload)
    {
        $updateService = [
            'type'          => $payload["type"],
            'name'          => $payload['name'],
            'price'         => $payload['price'],
            'duration'      => $payload['duration'],
            'description'   => $payload["description"],
            'company_id'    => $companyId
        ];

        return $this->client->update('services', [$updateService], "id=eq.$id&company_id=eq.$companyId");
    }

    public function delete(string $id)
    {
        return $this->client->delete('services', "id=eq.$id");
    }
}
