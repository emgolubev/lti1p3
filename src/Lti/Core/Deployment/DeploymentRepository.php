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

namespace App\Lti\Core\Deployment;

class DeploymentRepository implements DeploymentRepositoryInterface
{
    /** @var DeploymentInterface[] */
    private $deployments;

    public function __construct(array $deployments = [])
    {
        foreach ($deployments as $deployment) {
            $this->add($deployment);
        }
    }

    public function add(DeploymentInterface $deployment): self
    {
        $this->deployments[$deployment->getId()] = $deployment;

        return $this;
    }

    public function find(string $id): ?DeploymentInterface
    {
        return $this->deployments[$id] ?? null;
    }

    public function findByIssuer(string $issuer, string $clientId = null): ?DeploymentInterface
    {
        foreach ($this->deployments as $deployment) {
            if ($deployment->getPlatform()->getAudience() === $issuer) {
                return $deployment;
            }
        }

        return null;
    }

    public function findAll(): array
    {
        return $this->deployments;
    }
}
