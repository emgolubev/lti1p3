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

namespace App\Action\Tool\Security\DeepLink;

use App\Lti\Core\Exception\LtiException;
use App\Lti\Core\Message\MessageLaunchFactory;
use App\Lti\Core\Security\Message\MessageLaunchValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LaunchAction
{
    /** @var Environment */
    private $twig;

    /** @var MessageLaunchFactory */
    private $messageLaunchFactory;

    /** @var MessageLaunchValidator */
    private $messageLaunchValidator;

    public function __construct(
        Environment $twig,
        MessageLaunchFactory $launchFactory,
        MessageLaunchValidator $launchValidator
    ) {
        $this->twig = $twig;
        $this->messageLaunchFactory = $launchFactory;
        $this->messageLaunchValidator = $launchValidator;
    }

    /**
     * @throws LtiException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(Request $request): Response
    {
        // launch construction
        $launch = $this->messageLaunchFactory->create(
            $request->get('id_token'),
            $request->get('state'))
        ;

        // launch validation
        $launchValidationResult = $this->messageLaunchValidator->validate($launch);

        // launch rendering
        return new Response(
            $this->twig->render('tool/messageLaunchResult.html.twig', [
                'launch' => $launch,
                'launchValidationResult' => $launchValidationResult,
            ])
        );
    }
}
