<?php

declare(strict_types=1);

namespace App\Security\OAuth2\Repository;

use App\Security\OAuth2\Token\OAuth2AccessToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class OAuth2AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        return new OAuth2AccessToken();
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {

    }

    public function revokeAccessToken($tokenId): void
    {

    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return false;
    }
}
