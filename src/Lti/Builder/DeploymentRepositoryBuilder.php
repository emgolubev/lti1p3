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
use App\Lti\Core\Deployment\DeploymentRepository;
use App\Lti\Core\Platform\Platform;
use App\Lti\Core\Tool\Tool;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DeploymentRepositoryBuilder
{
    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function build(): DeploymentRepository
    {
        $repository = new DeploymentRepository();

        $platforms = $this->buildPlatforms();
        $tools = $this->buildTools();

        foreach ($this->parameterBag->get('deployments') as $identifier => $deployment) {
            $repository->add(
                new Deployment(
                    $identifier,
                    $platforms[$deployment['platform']],
                    $tools[$deployment['tool']]
                )
            );
        }

        return $repository;
    }

    private function buildPlatforms(): array
    {
        $platforms = [];

        foreach ($this->parameterBag->get('platforms') as $identifier => $data) {
            $platforms[$identifier] = new Platform(
                $identifier,
                $data['name'],
                $data['audience'],
                $data['oAuth2ClientId'],
                $data['oAuth2AccessTokenUrl'],
                $data['oidcAuthUrl'],
                $data['jwksUrl']
            );
        }

        return $platforms;
    }

    private function buildTools(): array
    {
        $tools = [];

        foreach ($this->parameterBag->get('tools') as $identifier => $data) {
            $tools[$identifier] = new Tool(
                $identifier,
                $data['name'],
                $data['oAuth2ClientId'],
                $data['deepLaunchUrl'],
                $data['oidcLoginInitiationUrl'],
                $data['jwksUrl']
            );
        }

        return $tools;
    }
}
