<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function login(string $phone, string $password): array
    {
        $res = $this->users->findByPhone($phone);

        

        if ($res['status'] !== 200 || empty($res['data'])) {
            return [
                'success' => false,
                'message' => 'Usuário não encontrado'
            ];
        }

        $user = $res['data'][0];

        if (!isset($user['password'])) {
            return [
                'success' => false,
                'message' => 'Senha não configurada para este usuário'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Senha incorreta',
                'password_provided' => $password,
                'password_stored' => $user['password']
            ];
        }

        return [
            'success' => true,
            'user' => [
                'id'         => $user['id'],
                'company_id' => $user['company_id'],
                'role'       => $user['role'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'phone'      => $user['phone']
            ]
        ];
    }
}
