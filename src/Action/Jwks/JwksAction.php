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

namespace App\Action\Jwks;

use App\Lti\Core\Security\Jwks\KeyChainExporter;
use App\Lti\Core\Security\Key\KeyChainRepositoryInterface;
use Lcobucci\JWT\Parsing\Encoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JwksAction
{
    /** @var Encoder */
    private $encoder;

    /** @var KeyChainRepositoryInterface */
    private $repository;

    /** @var KeyChainExporter */
    private $exporter;

    public function __construct(
        Encoder $encoder,
        KeyChainRepositoryInterface $repository,
        KeyChainExporter $exporter
    ) {
        $this->encoder = $encoder;
        $this->repository = $repository;
        $this->exporter = $exporter;
    }

    public function __invoke(string $identifier): Response
    {
        $chains = $this->repository->findByIdentifier($identifier);

        if (empty($chains)) {
            throw new NotFoundHttpException(sprintf("No JWK for chain identifier '%s'", $identifier));
        }

        $jwks = [];
        foreach ($chains as $chain) {
            $jwks[] = $this->exporter->export($chain);
        }

        return new JsonResponse(['keys' => $jwks]);
    }
}
