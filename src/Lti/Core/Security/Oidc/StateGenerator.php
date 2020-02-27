<?php

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace App\Lti\Core\Security\Oidc;

use App\Lti\Core\Deployment\DeploymentInterface;
use Carbon\Carbon;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Ramsey\Uuid\Uuid;

class StateGenerator implements StateGeneratorInterface
{
    /** @var Signer */
    private $signer;

    /** @var int */
    private $ttl;

    public function __construct(Signer $signer, int $ttl = null)
    {
        $this->signer =$signer;
        $this->ttl = $ttl ?? static::DEFAULT_TTL;
    }

    /**
     * @throws Exception
     */
    public function generate(DeploymentInterface $deployment, LoginInitiationRequest $request): string
    {
        $timestamp = Carbon::now()->getTimestamp();

        return (new Builder())
            ->identifiedBy(Uuid::uuid4())
            ->issuedAt($timestamp)
            ->expiresAt($timestamp + $this->ttl)
            ->issuedBy($deployment->getTool()->getName())
            ->relatedTo($deployment->getTool()->getOAuth2ClientId())
            ->permittedFor($deployment->getPlatform()->getOAuth2AccessTokenUrl())
            ->withClaim('params', [
                    'utf8' => true,
                    'iss' => $request->getIssuer(),
                    'login_hint' => $request->getLoginHint(),
                    'target_link_uri' => $request->getTargetLinkUri(),
                    'lti_message_hint' => $request->getLtiMessageHint(),
                    'lti_deployment_id' => $request->getLtiDeploymentId(),
                    'client_id' => $request->getClientId(),
            ])
            ->getToken($this->signer, $deployment->getToolContext()->getKeyChain()->getPrivateKey())
            ->__toString();
    }
}
