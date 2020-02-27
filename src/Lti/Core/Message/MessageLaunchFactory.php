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

use App\Lti\Core\Deployment\DeploymentRepositoryInterface;
use App\Lti\Core\Exception\LtiException;
use Lcobucci\JWT\Parser;
use Ramsey\Uuid\Uuid;
use Throwable;

class MessageLaunchFactory
{
    /** @var Parser */
    private $parser;

    /** @var DeploymentRepositoryInterface */
    private $deploymentRepository;

    public function __construct(Parser $parser, DeploymentRepositoryInterface $deploymentRepository)
    {
        $this->parser = $parser;
        $this->deploymentRepository = $deploymentRepository;
    }

    /**
     * @throws LtiException
     */
    public function create(string $token, string $state = null): MessageLaunchInterface
    {
        try {
            // parse token
            $jwtToken = $this->parser->parse($token);

            // extract deployment id claim from parsed token
            if (!$jwtToken->hasClaim(MessageLaunchInterface::CLAIM_LTI_DEPLOYMENT_ID)) {
                throw new LtiException(sprintf('Missing claim %s', MessageLaunchInterface::CLAIM_LTI_DEPLOYMENT_ID));
            }

            // find related deployment
            $deploymentId = $jwtToken->getClaim(MessageLaunchInterface::CLAIM_LTI_DEPLOYMENT_ID);
            $deployment = $this->deploymentRepository->find($deploymentId);

            if (null === $deployment) {
                throw new LtiException(sprintf('Cannot find deployment id %s', $deploymentId));
            }

            // return  created message
            return new MessageLaunch(
                Uuid::uuid4()->toString(),
                $deployment,
                $jwtToken,
                $state ? $this->parser->parse($state) : null
            );

        } catch (Throwable $exception) {
            throw new LtiException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
