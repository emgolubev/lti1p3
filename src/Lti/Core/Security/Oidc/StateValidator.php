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

use App\Lti\Core\Deployment\DeploymentInterface;
use App\Lti\Core\Exception\LtiException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Throwable;

class StateValidator
{
    /** @var Signer */
    private $signer;

    /** @var Parser */
    private $parser;

    public function __construct(Signer $signer, Parser $parser)
    {
        $this->signer = $signer;
        $this->parser = $parser;
    }

    /**
     * @throws LtiException
     */
    public function validate(DeploymentInterface $deployment, string $state): bool
    {
        try {
            $token = $this->parser->parse($state);

            return $token->verify(
                $this->signer,
                $deployment->getToolContext()->getKeyChain()->getPublicKey()
            );
        } catch (Throwable $exception) {
            throw new LtiException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
