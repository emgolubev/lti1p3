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

use App\Lti\Core\Security\OAuth2\OAuth2AccessTokenGenerator;
use Lcobucci\JWT\Parser;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class AccessTokenAction
{
    /** @var Parser */
    private $parser;

    /** @var HttpMessageFactoryInterface */
    private $psr7Factory;

    /** @var HttpFoundationFactoryInterface */
    private $httpFoundationFactory;

    /**  @var OAuth2AccessTokenGenerator */
    private $accessTokenGenerator;

    public function __construct(
        Parser $parser,
        HttpMessageFactoryInterface $psr7Factory,
        HttpFoundationFactoryInterface $httpFoundationFactory,
        OAuth2AccessTokenGenerator $accessTokenGenerator
    ) {
        $this->parser = $parser;
        $this->psr7Factory = $psr7Factory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->accessTokenGenerator = $accessTokenGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $psr7Response = $this->psr7Factory->createResponse(new Response());

        try {
            $psr7AuthenticationResponse = $this->accessTokenGenerator->generate(
                $this->psr7Factory->createRequest($request),
                $psr7Response
            );

            return $this->httpFoundationFactory->createResponse($psr7AuthenticationResponse);

        } catch (BadRequestHttpException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (OAuthServerException $exception) {
            return $this->httpFoundationFactory->createResponse($exception->generateHttpResponse($psr7Response));
        } catch (Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}
