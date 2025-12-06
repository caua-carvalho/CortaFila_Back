<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

class CompanyService
{
    private CompanyRepository $companies;
    private UserRepository $users;

    public function __construct()
    {
        $this->companies = new CompanyRepository();
        $this->users = new UserRepository();
    }

    private function normalizeResult(array $result): array
    {
        // Erro direto do Supabase ou falha de rede
        if (!empty($result['error'])) {
            return [
                'data'   => null,
                'error'  => $result['error'],
                'status' => $result['status'] ?? null
            ];
        }

        // Resposta sem "data" ou vazia → insert/select falhou silenciosamente
        if (!isset($result['data']) || empty($result['data'])) {
            return [
                'data'   => null,
                'error'  => 'Empty response from database',
                'status' => $result['status'] ?? null
            ];
        }

        // Resposta OK, retornar primeira linha (Supabase sempre retorna array)
        return [
            'data'   => $result['data'][0],
            'error'  => null,
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

        // 1) Criar empresa
        $companyResult = $this->companies->create($companyData);
        $companyNorm = $this->normalizeResult($companyResult);

        if ($companyNorm['error']) {
            return [
                'error'   => 'Company creation failed',
                'details' => $companyNorm
            ];
        }

        $company = $companyNorm['data'];
        $companyId = $company['id'];

        // 2) Criar admin vinculado à empresa
        $userData['company_id'] = $companyId;
        $userData['role'] = 'admin';

        $userResult = $this->users->create($userData);
        $userNorm = $this->normalizeResult($userResult);

        if ($userNorm['error']) {
            // rollback de empresa caso user falhe
            $this->companies->delete($companyId);

            return [
                'error'   => 'Admin user creation failed',
                'details' => $userNorm
            ];
        }

        $user = $userNorm['data'];

        // 3) Final response estruturada
        return [
            'company' => $company,
            'user'    => $user
        ];
    }
}
