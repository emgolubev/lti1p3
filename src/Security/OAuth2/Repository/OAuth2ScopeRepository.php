<?php declare(strict_types=1);
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */
namespace App\Security\OAuth2\Repository;

use App\Security\OAuth2\Entity\OAuth2Client;
use App\Security\OAuth2\Entity\OAuth2Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class OAuth2ScopeRepository implements ScopeRepositoryInterface
{

    public function getScopeEntityByIdentifier($identifier): ScopeEntityInterface
    {
        return new OAuth2Scope('scope');
    }

    /**
     * @inheritdoc
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $clientScopes = $this->getClientEntityScopes($clientEntity);

        if (empty($scopes)) {
            return $clientScopes;
        }

        foreach ($scopes as $requestedScope) {
            if (!in_array($requestedScope, $clientScopes)) {
                throw OAuthServerException::invalidScope($requestedScope->getIdentifier());
            }
        }

        return $scopes;
    }

    /**
     * @param ClientEntityInterface|OAuth2Client $clientEntity
     * @return ScopeEntityInterface[]
     */
    private function getClientEntityScopes(ClientEntityInterface $clientEntity): array
    {
        return array_map(
            function (string $role) {
                return $this->getScopeEntityByIdentifier($role);
            },
            $clientEntity->getRoles()
        );
    }
}
