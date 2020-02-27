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

namespace App\Lti\Core\Platform;

class Platform implements PlatformInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $audience;

    /** @var string */
    private $oAuth2ClientId;

    /** @var string */
    private $oAuth2AccessTokenUrl;

    /** @var string */
    private $oidcAuthenticationUrl;

    public function __construct(
        string $id,
        string $name,
        string $audience,
        string $oAuth2ClientId,
        string $oAuth2AccessTokenUrl,
        string $oidcAuthenticationUrl
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->audience = $audience;
        $this->oAuth2ClientId = $oAuth2ClientId;
        $this->oAuth2AccessTokenUrl = $oAuth2AccessTokenUrl;
        $this->oidcAuthenticationUrl = $oidcAuthenticationUrl;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAudience(): string
    {
        return $this->audience;
    }

    public function getOAuth2ClientId(): string
    {
        return $this->oAuth2ClientId;
    }

    public function getOAuth2AccessTokenUrl(): string
    {
        return $this->oAuth2AccessTokenUrl;
    }

    public function getOidcAuthenticationUrl(): string
    {
        return $this->oidcAuthenticationUrl;
    }
}
