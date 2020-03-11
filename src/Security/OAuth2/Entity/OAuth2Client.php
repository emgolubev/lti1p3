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
namespace App\Security\OAuth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class OAuth2Client implements ClientEntityInterface
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var string */
    private $redirectUri;

    /** @var string[] */
    private $roles;

    public function __construct(string $identifier, string $name, string $redirectUri, array $roles = [])
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->roles = $roles;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isConfidential(): bool
    {
        return false;
    }
}
