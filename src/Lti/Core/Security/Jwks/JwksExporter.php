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

namespace App\Lti\Core\Security\Jwks;

use App\Lti\Core\Security\Key\KeyChainInterface;
use Lcobucci\JWT\Parsing\Encoder;

class JwksExporter
{
    /** @var Encoder */
    private $encoder;

    public function __construct(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function exportKeyChains(array $keyChains): array
    {
        return [
            'keys' => array_map(
                function (KeyChainInterface $keyChain) {
                    return $this->exportKeyChain($keyChain);
                },
                $keyChains
            )
        ];
    }

    public function exportKeyChain(KeyChainInterface $keyChain): array
    {
        $components = openssl_pkey_get_details(
            openssl_pkey_get_public($keyChain->getPublicKey()->getContent())
        );

        return [
            'alg' => 'RS256',
            'kty' => 'RSA',
            'use' => 'sig',
            'n' => $this->encoder->base64UrlEncode($components['rsa']['n']),
            'e' => $this->encoder->base64UrlEncode($components['rsa']['e']),
            'kid' => $keyChain->getId(),
        ];
    }
}
