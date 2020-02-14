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

namespace App\Lti\Core\Security\Key;

class KeyChainRepository implements KeyChainRepositoryInterface
{
    /** @var KeyChain[][] */
    private $chains;

    public function __construct(array $chains = [])
    {
        foreach ($chains as $identifier => $chain) {
            $this->add($identifier, $chain);
        }
    }

    public function add(string $identifier, KeyChain $chain): self
    {
        $this->chains[$identifier][] = $chain;

        return $this;
    }

    public function findByIdentifier(string $identifier): array
    {
        return $this->chains[$identifier] ?? [];
    }
}
