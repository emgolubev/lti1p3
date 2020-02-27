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

use App\Form\Platform\Security\Oidc\LoginRequestType;
use App\Lti\Core\Deployment\DeploymentInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class LoginRequestAction
{
    /** @var Environment */
    private $twig;

    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(Environment $twig, FormFactoryInterface  $formFactory)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(LoginRequestType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            /** @var DeploymentInterface $deployment */
            $deployment = $formData['deployment'];

            return new RedirectResponse(
                $deployment->getTool()->getOidcLoginInitiationUrl() . '?' . http_build_query([
                    'iss' => $deployment->getPlatform()->getAudience(),
                    'login_hint' => $formData['login_hint'],
                    'target_link_uri' => $deployment->getTool()->getDeepLaunchUrl(),
                    'lti_message_hint' => $formData['lti_message_hint'],
                    'lti_deployment_id' => $deployment->getId(),
                    'client_id' => $deployment->getPlatform()->getOAuth2ClientId()
                ])
            );
        }

        return new Response(
            $this->twig->render('platform/messageLaunch.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
