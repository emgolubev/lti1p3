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

namespace App\Lti\Core\Message;

use App\Lti\Core\Deployment\DeploymentInterface;
use Lcobucci\JWT\Token;

class MessageLaunch implements MessageLaunchInterface
{
    /** @var string */
    private $id;

    /** @var DeploymentInterface */
    private $deployment;

    /** @var Token */
    private $token;

    /** @var Token|null */
    private $state;

    public function __construct(
        string $id,
        DeploymentInterface $deployment,
        Token $token,
        Token $state = null
    ) {
        $this->id = $id;
        $this->deployment = $deployment;
        $this->token = $token;
        $this->state = $state;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDeployment(): DeploymentInterface
    {
        return $this->deployment;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getState(): ?Token
    {
        return $this->state;
    }

    public function getHeaders(): array
    {
        return $this->token->getHeaders();
    }

    public function getHeader(string $headerName, $default = null)
    {
        if ($this->token->hasHeader($headerName)) {
            return $this->token->getHeader($headerName);
        }

        return $default;
    }

    public function getClaims(): array
    {
        return $this->token->getClaims();
    }

    public function getClaim(string $claimName, $default = null)
    {
        if ($this->token->hasClaim($claimName)) {
            return $this->token->getClaim($claimName);
        }

        return $default;
    }

    public function getMessageType(): string
    {
        return (string)$this->getClaim(self::CLAIM_LTI_MESSAGE_TYPE);
    }

    public function getVersion(): string
    {
        return (string)$this->getClaim(self::CLAIM_LTI_VERSION);
    }

    public function getDeploymentId(): string
    {
        return (string)$this->getClaim(self::CLAIM_LTI_DEPLOYMENT_ID);
    }

    public function getTargetLinkUri(): string
    {
        return (string)$this->getClaim(self::CLAIM_LTI_TARGET_LINK_URI);
    }

    public function getRoles(): array
    {
        return (array)$this->getClaim(self::CLAIM_LTI_ROLES);
    }

    public function getResourceLink(): ?array
    {
        return (array)$this->getClaim(self::CLAIM_LTI_RESOURCE_LINK);
    }

    public function isAnonymous(): bool
    {
        return !$this->token->hasClaim(self::CLAIM_SUB);
    }

    public function isOidcLoginInitiation(): bool
    {
        return null !== $this->state;
    }
}
