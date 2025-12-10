<?php

namespace App\Repositories;

use App\Services\SupabaseClient;

class InviteRepository
{
    private SupabaseClient $client;

    public function __construct()
    {
        $this->client = new SupabaseClient();
    }

    public function create(array $data)
    {
        return $this->client->insert('invites', [$data]);
    }

    public function findValidToken(string $token)
    {
        return $this->client->select('invites', [
            'token' => "eq.$token",
            'used_at' => "is.null", // Supabase aceita "is.null"
            // Não dá para fazer expires_at > NOW() direto aqui
        ]);
    }

    public function markUsed(int $inviteId)
    {
        return $this->client->update(
            'invites',
            ['used_at' => date('c')],
            "id=eq.$inviteId"
        );
    }

    public function findByUser(int $userId)
    {
        return $this->client->select('invites', [
            'user_id' => "eq.$userId"
        ]);
    }
}
