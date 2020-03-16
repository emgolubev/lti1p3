<?php

declare(strict_types=1);

namespace App\Security\OAuth2\Repository;

use App\Security\OAuth2\Entity\OAuth2Client;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class OAuth2ClientRepository implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?OAuth2Client
    {
        return new OAuth2Client(
            'id',
            'name',
            'redirectUri',
            ['Admin']
        );
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        return true;
    }
}
