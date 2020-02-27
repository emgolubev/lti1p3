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

use App\Lti\Core\Deployment\DeploymentRepositoryInterface;
use App\Lti\Core\Exception\LtiException;
use App\Lti\Core\Security\Nonce\NonceGeneratorInterface;
use App\Lti\Core\Security\Nonce\NonceInterface;
use App\Lti\Core\Security\Nonce\NonceRepositoryInterface;

class LoginInitiator
{
    /** @var StateGeneratorInterface */
    private $stateGenerator;

    /** @var NonceGeneratorInterface */
    private $nonceGenerator;

    /** @var DeploymentRepositoryInterface */
    private $deploymentRepository;

    /** @var NonceRepositoryInterface */
    private $nonceRepository;

    public function __construct(
        StateGeneratorInterface $stateGenerator,
        NonceGeneratorInterface $nonceGenerator,
        DeploymentRepositoryInterface $deploymentRepository,
        NonceRepositoryInterface $nonceRepository
    ) {
        $this->stateGenerator = $stateGenerator;
        $this->nonceGenerator = $nonceGenerator;
        $this->deploymentRepository = $deploymentRepository;
        $this->nonceRepository = $nonceRepository;
    }

    /**
     * @throws LtiException
     */
    public function initiate(LoginInitiationRequest $request): LoginInitiationResponse
    {
        $deployment = $this->deploymentRepository->findByIssuer($request->getIssuer());

        if (null === $deployment) {
            throw new LtiException(
                sprintf('No deployment found for issuer  %s', $request->getIssuer())
            );
        }

        return new LoginInitiationResponse(
            $deployment->getPlatform()->getOidcAuthenticationUrl(),
            [
                'response_type' => 'id_token',
                'redirect_uri' => $request->getTargetLinkUri(),
                'response_mode' => 'form_post',
                'client_id' => $deployment->getTool()->getOAuth2ClientId(),
                'scope' => 'openid',
                'state' => $this->stateGenerator->generate($deployment, $request),
                'login_hint' => $request->getLoginHint(),
                'lti_message_hint' => $request->getLtiMessageHint(),
                'prompt' => 'none',
                'nonce' => $this->generateNonce()->getValue()
            ]
        );
    }

    private function generateNonce(): NonceInterface
    {
        $nonce = $this->nonceGenerator->generate();

        $this->nonceRepository->save($nonce);

        return $nonce;
    }
}
