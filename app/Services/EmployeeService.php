<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\InviteRepository;
use App\Services\MailService;

class EmployeeService
{
    private UserRepository $users;
    private InviteRepository $invites;
    private MailService $mailer;

    public function __construct()
    {
        $this->users   = new UserRepository();
        $this->invites = new InviteRepository();
        $this->mailer  = new MailService();
    }

    /**
     * Cria funcionário (user.role = 'employee') e envia convite.
     */
    public function createEmployee(array $payload, int $companyId): array
    {
        if (
            empty($payload['name']) ||
            empty($payload['email']) ||
            empty($payload['phone'])
        ) {
            return ['success' => false, 'message' => 'Dados incompletos.'];
        }

        $exists = $this->users->findByEmailAndCompany($payload['email'], $companyId);

        if (!empty($exists['data'])) {
            return ['success' => false,
                    'message' => 'Já existe um funcionário com este e-mail.',
                    'inputs'  => 'email'
                   ];
        }

        $newUser = [
            'name'       => $payload['name'],
            'email'      => $payload['email'],
            'phone'      => $payload['phone'],
            'company_id' => $companyId,
            'role'       => 'employee',
            'password'   => null,
            'status'     => 'pending'
        ];

        $userInsert = $this->users->create($newUser);

        if (empty($userInsert['data'][0]['id'])) {
            return ['success' => false, 'message' => 'Erro ao criar funcionário.'];
        }

        $userId = $userInsert['data'][0]['id'];
        $token  = bin2hex(random_bytes(32));

        $invitePayload = [
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => date('c', strtotime('+48 hours'))
        ];

        $inviteInsert = $this->invites->create($invitePayload);

        if (!empty($inviteInsert['error'])) {
            return ['success' => false, 'message' => 'Erro ao gerar convite.'];
        }

        $frontendUrl = $_ENV['FRONTEND_URL'] ?? null;

        if (!$frontendUrl) {
            return ['success' => false, 'message' => 'FRONTEND_URL não definido no .env'];
        }

        $inviteLink = "{$frontendUrl}/definir-senha?token={$token}";

        $emailSent = $this->mailer->sendEmployeeInvite(
            to: $payload['email'],
            name: $payload['name'],
            inviteLink: $inviteLink
        );

        if (!$emailSent) {
            return [
                'success' => false,
                'message' => 'Funcionário criado, mas falha ao enviar o e-mail.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Funcionário criado e convite enviado por e-mail.'
        ];
    }

    /**
     * Ativa usuário via token e define senha.
     */
    public function activateEmployee(string $token, string $password): array
    {
        $invite = $this->invites->findValidToken($token);

        if (empty($invite['data'][0])) {
            return [
                'success' => false,
                'message' => 'Convite inválido ou expirado.'
            ];
        }

        $inviteData = $invite['data'][0];

        if ($inviteData['expires_at'] <= date('c')) {
            return [
                'success' => false,
                'message' => 'Convite expirado.'
            ];
        }

        $userId = $inviteData['user_id'];
        $hash   = password_hash($password, PASSWORD_BCRYPT);

        $update = $this->users->activateUser($userId, $hash);

        if (!empty($update['error'])) {
            return [
                'success' => false,
                'message' => 'Erro ao ativar conta.'
            ];
        }

        $this->invites->markUsed($inviteData['id']);

        return [
            'success' => true,
            'message' => 'Conta ativada com sucesso.'
        ];
    }

    public function findByToken($token)
    {
        $invite = $this->invites->findValidToken($token);

        if (empty($invite['data'][0])) {
            return [
                'success' => false,
                'message' => 'Convite inválido ou expirado.'
            ];
        }

        $inviteData = $invite['data'][0];

        if ($inviteData['expires_at'] <= date('c')) {
            return [
                'success' => false,
                'message' => 'Convite expirado.'
            ];
        }

        $user = $this->users->findById($inviteData['user_id']);

        return [
            'user' => $user
        ];
    }
}
