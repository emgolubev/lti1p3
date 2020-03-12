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
 *q
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */
namespace App\Security\OAuth2\Factory;

use App\Security\OAuth2\Grant\CorrectJwtBearerGrant;
use App\Security\OAuth2\Repository\OAuth2AccessTokenRepository;
use App\Security\OAuth2\Repository\OAuth2ClientRepository;
use App\Security\OAuth2\Repository\OAuth2ScopeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use OAT\Library\Lti1p3Core\Security\Oauth2\JwtClientCredentialsGrant;

class OAuth2AuthorizationServerFactory
{
    /** @var OAuth2AccessTokenRepository */
    private $accessTokenRepository;

    /** @var OAuth2ClientRepository */
    private $clientRepository;

    /** @var OAuth2ScopeRepository */
    private $scopeRepository;

    /** @var CryptKey */
    private $privateKey;

    /** @var string */
    private $encryptionKey;

    public function __construct(
        OAuth2AccessTokenRepository $accessTokenRepository,
        OAuth2ClientRepository $clientRepository,
        OAuth2ScopeRepository $scopeRepository,
        CryptKey $privateKey,
        string $encryptionKey
    ) {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->privateKey = $privateKey;
        $this->encryptionKey = $encryptionKey;
    }

    public function create(): AuthorizationServer
    {
        $server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $this->privateKey,
            $this->encryptionKey
        );

        $server->enableGrantType(new JwtClientCredentialsGrant());

        return $server;
    }
}
