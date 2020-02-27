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

namespace App\Lti\Core\Message\Context;

class LtiContext
{
    /** @var string */
    private $messageType;

    /** @var string */
    private $ltiVersion;

    /** @var string */
    private $ltiDeploymentId;

    /** @var string */
    private $targetLinkUri;

    /** @var string */
    private $roles;

    /** @var array|null */
    private $resourceLink;

    /** @var array|null */
    private $context;

    /** @var array|null */
    private $platformInstance;

    /** @var array|null */
    private $roleScopeMentor;

    /** @var array|null */
    private $launchPresentation;


    /** @var array|null */
    private $custom;

    public function __construct(
        string $messageType,
        string $ltiVersion,
        string $ltiDeploymentId,
        string $targetLinkUri,
        array $roles,
        array $resourceLink = null,
        array $context = null,
        array $platformInstance = null,
        array $roleScopeMentor = null,
        array $launchPresentation = null,
        array $custom = null
    ) {
        $this->messageType = $messageType;
        $this->ltiVersion = $ltiVersion;
        $this->ltiDeploymentId = $ltiDeploymentId;
        $this->targetLinkUri = $targetLinkUri;
        $this->resourceLink = $resourceLink;
        $this->roles = $roles;
        $this->context = $context;
        $this->platformInstance = $platformInstance;
        $this->roleScopeMentor = $roleScopeMentor;
        $this->launchPresentation = $launchPresentation;
        $this->custom = $custom;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getLtiVersion(): string
    {
        return $this->ltiVersion;
    }

    public function getLtiDeploymentId(): string
    {
        return $this->ltiDeploymentId;
    }

    public function getTargetLinkUri(): string
    {
        return $this->targetLinkUri;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getResourceLink(): ?array
    {
        return $this->resourceLink;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function getPlatformInstance(): ?array
    {
        return $this->platformInstance;
    }

    public function getRoleScopeMentor(): ?array
    {
        return $this->roleScopeMentor;
    }

    public function getLaunchPresentation(): ?array
    {
        return $this->launchPresentation;
    }

    public function getCustom(): ?array
    {
        return $this->custom;
    }
}
