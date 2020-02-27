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

namespace App\Action\Platform\Security\Oidc;

use App\Lti\Core\Deployment\DeploymentInterface;
use App\Lti\Core\Deployment\DeploymentRepositoryInterface;
use App\Lti\Core\Message\MessageLaunchInterface;
use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AuthorizationAction
{
    /** @var Environment */
    private $twig;

    /** @var Parser */
    private $parser;

    /** @var Signer */
    private $signer;

    /** @var DeploymentRepositoryInterface */
    private $deploymentRepository;

    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(
        Environment $twig,
        Parser $parser,
        Signer $signer,
        DeploymentRepositoryInterface $deploymentRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->twig = $twig;
        $this->parser = $parser;
        $this->signer = $signer;
        $this->deploymentRepository = $deploymentRepository;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(Request $request): Response
    {
        $state = $this->parser->parse($request->get('state'));

        $parameters = (array)$state->getClaim('params');

        $deployment = $this->deploymentRepository->find($parameters['lti_deployment_id']);

        $user = $this->parameterBag->get('users')[$parameters['login_hint']];

        return new Response(
            $this->twig->render('platform/messageLaunchAuthorization.html.twig', [
                'redirectUri' => $deployment->getTool()->getDeepLaunchUrl(),
                'idToken' => $this->generateIdToken($deployment, $user),
                'state' => $request->get('state')
            ])
        );
    }

    private function generateIdToken(DeploymentInterface $deployment, array $userData): string
    {
        $timestamp = Carbon::now()->getTimestamp();

        return (new Builder())
            ->withHeader('kid', $deployment->getPlatformContext()->getKeyChain()->getId())
            ->identifiedBy(Uuid::uuid4())
            ->issuedAt($timestamp)
            ->expiresAt($timestamp + 500)
            ->issuedBy($deployment->getPlatform()->getAudience())
            ->relatedTo($deployment->getTool()->getOAuth2ClientId())
            ->permittedFor($deployment->getTool()->getOAuth2ClientId())
            ->withClaim(MessageLaunchInterface::CLAIM_NONCE, Uuid::uuid4())
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_DEPLOYMENT_ID, $deployment->getId())
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_VERSION, '1.3.0')
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_MESSAGE_TYPE, 'LtiResourceLinkRequest')
            ->withClaim(MessageLaunchInterface::CLAIM_LTI_ROLES, [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner',
                'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Student',
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor'
            ])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_NAME, $userData['name'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_EMAIL, $userData['email'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_GIVEN_NAME, $userData['givenName'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_FAMILY_NAME, $userData['familyName'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_MIDDLE_NAME, $userData['middleName'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_LOCALE, $userData['locale'])
            ->withClaim(MessageLaunchInterface::CLAIM_USER_PICTURE, $userData['picture'])
            ->getToken($this->signer, $deployment->getPlatformContext()->getKeyChain()->getPrivateKey())
            ->__toString();
    }
}
