<?php

declare(strict_types=1);

namespace App\Lti\Core\Security\OAuth2;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;

class CustomBearerResponseType extends BearerTokenResponse
{
    /**
     * {@inheritdoc}
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        return [
            'scope' => join(' ', array_map(function (ScopeEntityInterface $scope) {
                return $scope->getIdentifier();
            }, $accessToken->getScopes()))
        ];
    }
}
