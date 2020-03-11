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

namespace App\Action\Platform\Security\OAuth2;

use Lcobucci\JWT\Parser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AccessTokenAction
{
    /** @var Parser */
    private $parser;

    /** @var HttpMessageFactoryInterface */
    private $psr7Factory;

    /** @var HttpFoundationFactoryInterface */
    private $httpFoundationFactory;

    /** @var AuthorizationServer */
    private $authorizationServer;

    public function __construct(Parser $parser, HttpMessageFactoryInterface $psr7Factory, HttpFoundationFactoryInterface $httpFoundationFactory, AuthorizationServer $authorizationServer)
    {
        $this->parser = $parser;
        $this->psr7Factory = $psr7Factory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->authorizationServer = $authorizationServer;
    }

    public function __invoke(Request $request): Response
    {
        $psr7Response = $this->psr7Factory->createResponse(new Response());

        try {
            $this->validateParameters($request);

            $this->validateAssertion($request);

            $psr7AuthenticationResponse = $this->authorizationServer->respondToAccessTokenRequest(
                $this->psr7Factory->createRequest($request),
                $psr7Response
            );

            return $this->httpFoundationFactory->createResponse($psr7AuthenticationResponse);

        } catch (BadRequestHttpException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (OAuthServerException $exception) {
            return $this->httpFoundationFactory->createResponse($exception->generateHttpResponse($psr7Response));
        }
    }

    private function getRequestParameter(Request $request, string $parameterName, bool $isRequired = true): ?string
    {
        $parameterValue = $request->get($parameterName);

        if ($isRequired && null === $parameterValue) {
            throw new BadRequestHttpException(
                sprintf('Parameter %s is required', $parameterName)
            );
        }

        return $parameterValue;
    }

    private function validateParameters(Request $request): void
    {
        $grant_type = $this->getRequestParameter($request, 'grant_type');
        $client_assertion_type = $this->getRequestParameter($request, 'client_assertion_type');
        $scopes = $this->getRequestParameter($request, 'scope');

        // validation
        if ('client_credentials' !== $grant_type) {
            throw new BadRequestHttpException('Only Client credentials grant type is supported');
        }

        if ('urn:ietf:params:oauth:client-assertion-type:jwt-bearer' !== $client_assertion_type) {
            throw new BadRequestHttpException('Incorrect client assertion is provided');
        }

        if (is_string($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        // TODO: how validate scopes?
        // in phpleague we have a method https://github.com/thephpleague/oauth2-server/blob/master/src/Grant/AbstractGrant.php#L288
    }

    protected function validateAssertion(Request $request): array
    {
        $client_assertion = $this->parser->parse($this->getRequestParameter($request, 'client_assertion'));

        // TODO: need validate signature
        // https://github.com/MilesChou/oauth2-server-jwt-bearer-grant/blob/master/src/JwtBearerGrant.php#L155

        $claims = $client_assertion->getClaims();

        // TODO: validate claims
        // we can use package "web-token/jwt-checker"


        return $claims;
    }
}
