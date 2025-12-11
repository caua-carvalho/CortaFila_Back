<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

class CompanyService
{
    private CompanyRepository $companies;
    private UserRepository $users;
    private \App\Services\SupabaseClient $supabase;

    public function __construct()
    {
        $this->companies = new CompanyRepository();
        $this->users = new UserRepository();
        $this->supabase = new \App\Services\SupabaseClient();
    }

    private function normalizeResult(array $result): array
    {
        // Erro direto do Supabase
        if (!empty($result['error'])) {
            return [
                'data'   => null,
                'error'  => $result['error'],
                'status' => $result['status'] ?? null
            ];
        }

        // Caso RPC: retorno direto em result['data'] (objeto)
        if (isset($result['data']) && !isset($result['data'][0])) {
            return [
                'data'   => $result['data'], // é um único objeto
                'error'  => null,
                'status' => $result['status'] ?? null
            ];
        }

        // Caso retorno padrão Supabase (arrays)
        if (isset($result['data'][0])) {
            return [
                'data'   => $result['data'][0],
                'error'  => null,
                'status' => $result['status'] ?? null
            ];
        }

        return [
            'data'   => null,
            'error'  => 'Empty response from database',
            'status' => $result['status'] ?? null
        ];
    }




    public function createCompanyWithAdmin(array $payload): array
    {
        $companyData = $payload['company'] ?? null;
        $userData    = $payload['user'] ?? null;

        if (!$companyData || !$userData) {
            return [
                'error' => 'Invalid payload structure. Expected { company: {}, user: {} }'
            ];
        }

        // Sanitização mínima
        $companyName   = $companyData['name']   ?? null;
        $companyAddress = $companyData['address'] ?? null;

        $userName  = $userData['name']  ?? null;
        $userEmail = $userData['email'] ?? null;
        $userPhone = $userData['phone'] ?? null;
        $password  = $userData['password'] ?? null;

        if (!$companyName || !$userName || !$userEmail || !$password) {
            return [
                'error' => 'Missing required fields for company or user.'
            ];
        }

        // Hash da senha ANTES de chamar o banco
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Payload RPC para o Supabase
        $rpcPayload = [
            "p_company_name"   => $companyName,
            "p_company_address" => $companyAddress,
            "p_user_name"      => $userName,
            "p_user_email"     => $userEmail,
            "p_user_phone"     => $userPhone,
            "p_user_password"  => $hashedPassword
        ];

        // Chamada RPC
        $response = $this->supabase->rpc("create_company_with_admin", $rpcPayload);

        $norm = $this->normalizeResult($response);

        if ($norm['error']) {
            return [
                'error'   => 'Failed to create company/admin via RPC',
                'details' => $norm
            ];
        }

        // Sucesso
        return $norm['data'];
    }
}
