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
use App\Lti\Core\Message\MessageLaunchInterface;
use Carbon\Carbon;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Ramsey\Uuid\Uuid;

class TokenGenerator
{
    /** @var Signer */
    private $signer;

    public function __construct(Signer $signer)
    {
        $this->signer =$signer;
    }

    /**
     * @throws Exception
     */
    public function generate(DeploymentInterface $deployment, array $claims = []): string
    {
        $timestamp = Carbon::now()->getTimestamp();

        return (new Builder())
            ->identifiedBy(Uuid::uuid4())
            ->issuedAt($timestamp)
            ->expiresAt($timestamp + 500)
            ->issuedBy($deployment->getTool()->getName())
            ->relatedTo($deployment->getTool()->getOAuth2ClientId())
            ->permittedFor($deployment->getPlatform()->getOAuth2AccessTokenUrl())
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_DEPLOYMENT_ID, $deployment->getId())
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_VERSION, '1.3.0')
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_MESSAGE_TYPE, 'LtiResourceLinkRequest')
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_ROLES, [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Student',
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor'
            ])
            ->getToken($this->signer, $deployment->getPlatformContext()->getKeyChain()->getPrivateKey())
            ->__toString();
    }
}
