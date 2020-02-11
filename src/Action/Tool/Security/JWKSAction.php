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

namespace App\Action\Tool\Security;

use Lcobucci\JWT\Parsing\Encoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JWKSAction
{
    /** @var Encoder */
    private $encoder;

    /**
     * JWKSAction constructor.
     * @param Encoder $encoder
     */
    public function __construct(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }


    public function __invoke(): Response
    {
        $privateKey = openssl_pkey_get_public(
            file_get_contents(__DIR__ . '/../../../../config/keys/tool/public.key')
        );

        $details = openssl_pkey_get_details($privateKey);

        $components = array(
            'kty' => 'RSA',
            'alg' => 'RS256',
            'use' => 'sig',
            'e' => $this->encoder->base64UrlEncode($details['rsa']['e']),
            'n' => $this->encoder->base64UrlEncode($details['rsa']['n']),
            'kid' => '1234',
        );

        return new JsonResponse($components);
    }
}
