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

    public function findByEmailAndCompany(string $email, int $companyId)
    {
        return $this->client->select('users', [
            'email' => "eq.$email",
            'company_id' => "eq.$companyId"
        ]);
    }

    public function findByPhone(string $phone)
    {
        return $this->client->select('users', [
            'phone' => "eq.$phone"
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

    public function activateUser(int $userId, string $passwordHash)
    {
        return $this->client->update(
            'users',
            [
                'status'   => 'active',
                'password' => $passwordHash
            ],
            "id=eq.$userId"
        );
    }

    public function delete(string $id)
    {
        return $this->client->delete('users', "id=eq.$id");
    }
}
