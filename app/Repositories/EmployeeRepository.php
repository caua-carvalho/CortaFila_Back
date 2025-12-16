<?php

namespace App\Repositories;

use App\Services\SupabaseClient;

class EmployeeRepository
{
    private SupabaseClient $client;

    public function __construct()
    {
        $this->client = new SupabaseClient();
    }

    public function all(string $companyId)
    {
        return $this->client->select('professionals', [
            'company_id' => "eq.$companyId",
            'select' => '
            id,
            name,
            phone,
            avatar,
            users (
                email,
                role,
                status,
                created_at
            ),
            professional_working_hours (
                day,
                start_time,
                end_time
            )
        '
        ]);
    }


    public function findByEmailAndCompany(string $email, int $companyId)
    {
        return $this->client->select('user', [
            'email' => "eq.$email",
            'company_id' => "eq.$companyId"
        ]);
    }

    public function findByPhone(string $phone)
    {
        return $this->client->select('user', [
            'phone' => "eq.$phone"
        ]);
    }

    public function create(array $data)
    {
        return $this->client->insert('user', [$data]);
    }

    public function update(string $id, array $data)
    {
        return $this->client->update('user', [$data], "id=eq.$id");
    }

    public function activateUser(int $userId, string $passwordHash)
    {
        return $this->client->update(
            'user',
            [
                'status'   => 'active',
                'password' => $passwordHash
            ],
            "id=eq.$userId"
        );
    }

    public function delete(string $id)
    {
        return $this->client->delete('user', "id=eq.$id");
    }
}
