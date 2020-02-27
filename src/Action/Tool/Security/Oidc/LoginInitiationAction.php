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

use App\Lti\Core\Exception\LtiExceptionInterface;
use App\Lti\Core\Security\Oidc\LoginInitiationRequest;
use App\Lti\Core\Security\Oidc\LoginInitiator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LoginInitiationAction
{
    /** @var LoginInitiator */
    private $loginInitiator;

    public function __construct(LoginInitiator $loginInitiator)
    {
        $this->loginInitiator = $loginInitiator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $request = new LoginInitiationRequest(
            $this->getRequestParameter($request, 'iss'),
            $this->getRequestParameter($request, 'login_hint'),
            $this->getRequestParameter($request, 'target_link_uri'),
            $this->getRequestParameter($request, 'lti_message_hint', false),
            $this->getRequestParameter($request, 'lti_deployment_id', false),
            $this->getRequestParameter($request, 'client_id', false)
        );

        try {
            $response = $this->loginInitiator->initiate($request);
        } catch (LtiExceptionInterface $exception) {
            throw new UnauthorizedHttpException('LTI1p3', $exception->getMessage(), $exception);
        }

        return new RedirectResponse($response ->__toString());
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
}
