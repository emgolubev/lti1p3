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

namespace App\Action\Tool\Security\Oidc;

use App\Lti\Core\Deployment\DeploymentRepositoryInterface;
use App\Lti\Core\Security\Key\KeyChainRepositoryInterface;
use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginInitiationAction
{
    /** @var DeploymentRepositoryInterface */
    private $deploymentRepository;

    /** @var KeyChainRepositoryInterface */
    private $keyChainRepository;

    public function __construct(
        DeploymentRepositoryInterface $repository,
        KeyChainRepositoryInterface $keyChainRepository
    ) {
        $this->deploymentRepository = $repository;
        $this->keyChainRepository = $keyChainRepository;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        // request
        $iss = $request->get('iss');                         // todo: validate as required
        $loginHint = $request->get('login_hint');            // todo: validate as required
        $targetLinkUri = $request->get('target_link_uri');   // todo: validate as required
        $ltiMessageHint = $request->get('lti_message_hint');

        // time controls
        $timestamp = Carbon::now()->getTimestamp();
        $ttl = 3600;
        $nonce = Uuid::uuid4();

        // find registration
        $deployment = $this->deploymentRepository->findByIssuer($iss);
        $keyChains = $this->keyChainRepository->findByIdentifier($deployment->getTool()->getId());

        // state
        $params = [
            'utf8' => true,
            'iss' => $iss,
            'login_hint' => $loginHint,
            'target_link_uri' => $targetLinkUri,
            'lti_message_hint' => $ltiMessageHint
        ];

        $token = (new Builder())
            ->identifiedBy(Uuid::uuid4())
            ->issuedAt($timestamp)
            ->expiresAt($timestamp + $ttl)
            ->issuedBy($deployment->getTool()->getName())
            ->relatedTo($deployment->getTool()->getOAuth2ClientId())
            ->permittedFor($deployment->getPlatform()->getOAuth2AccessTokenUrl())
            ->withClaim('params', $params)
            ->getToken(new Sha256(), current($keyChains)->getPrivateKey());


        $authParams = [
            'response_type' => 'id_token',
            'redirect_uri' => $targetLinkUri,
            'response_mode' => 'form_post',
            'client_id' => $deployment->getTool()->getOAuth2ClientId(),
            'scope' => 'openid',
            'state' => $token->__toString(),
            'login_hint' => $loginHint,
            'lti_message_hint' => $ltiMessageHint,
            'prompt' => 'none',
            'nonce' => $nonce->toString()
        ];

        return new RedirectResponse(sprintf(
            '%s?%s',
            $deployment->getPlatform()->getOidcAuthenticationUrl(),
            http_build_query($authParams)
        ));
    }
}
