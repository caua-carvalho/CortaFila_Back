<?php

namespace App\Services;

use App\Repositories\UserRepository;

class EmployeeService
{
    private UserRepository $users;
    private \App\Services\SupabaseClient $supabase;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->supabase = new \App\Services\SupabaseClient();
    }

    public function createEmployee(array $payload, int $companyId)
    {
        // validações mínimas
        if (
            empty($payload['name']) ||
            empty($payload['email']) ||
            empty($payload['phone'])
        ) {
            return [
                'success' => false,
                'message' => 'Dados incompletos para criação de funcionário'
            ];
        }

        $newUser = [
            'name'       => $payload['name'],
            'email'      => $payload['email'],
            'phone'      => $payload['phone'],
            'company_id' => $companyId, // sempre do JWT
            'role'       => 'employee',
            'password'   => isset($payload['password'])
                ? password_hash($payload['password'], PASSWORD_BCRYPT)
                : null
        ];

        return $this->users->create($newUser);
    }
}
