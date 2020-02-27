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

namespace App\Lti\Builder;

use App\Lti\Core\Deployment\Deployment;
use App\Lti\Core\Deployment\DeploymentContext;
use App\Lti\Core\Deployment\DeploymentRepository;
use App\Lti\Core\Platform\PlatformRepositoryInterface;
use App\Lti\Core\Security\Key\KeyChainRepositoryInterface;
use App\Lti\Core\Tool\ToolRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DeploymentRepositoryBuilder
{
    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var PlatformRepositoryInterface */
    private $platformRepository;

    /** @var ToolRepositoryInterface */
    private $toolRepository;

    /** @var KeyChainRepositoryInterface */
    private $keyChainRepository;

    public function __construct(
        ParameterBagInterface $parameterBag,
        PlatformRepositoryInterface $platformRepository,
        ToolRepositoryInterface $toolRepository,
        KeyChainRepositoryInterface $keyChainRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->platformRepository = $platformRepository;
        $this->toolRepository = $toolRepository;
        $this->keyChainRepository = $keyChainRepository;
    }

    public function build(): DeploymentRepository
    {
        $repository = new DeploymentRepository();

        foreach ($this->parameterBag->get('deployments') as $identifier => $data) {
            $repository->add(
                new Deployment(
                    $identifier,
                    $this->platformRepository->find($data['platform']['id']),
                    $this->toolRepository->find($data['tool']['id']),
                    new DeploymentContext(
                        $this->keyChainRepository->find($data['platform']['keyChain'] ?? ''),
                        $data['platform']['jwksUrl'] ?? null
                    ),
                    new DeploymentContext(
                        $this->keyChainRepository->find($data['tool']['keyChain'] ?? ''),
                        $data['tool']['jwksUrl'] ?? null
                    )
                )
            );
        }

        return $repository;
    }
}
