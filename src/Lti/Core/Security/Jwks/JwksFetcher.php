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

use App\Lti\Core\Exception\LtiException;
use CoderCat\JWKToPEM\JWKConverter;
use Lcobucci\JWT\Signer\Key;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class JwksFetcher implements JwksFetcherInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var JWKConverter */
    private $converter;

    public function __construct(HttpClientInterface $client, JWKConverter $converter)
    {
        $this->client = $client;
        $this->converter = $converter;
    }
    /**
     * @throws LtiException
     */
    public function fetchKey(string $jwksUrl, string $kId): Key
    {
        try {
            $response = $this->client
                ->request('GET', $jwksUrl, ['json' => true])
                ->toArray();

            foreach ($response['keys'] as $data) {
                if ($data['kid'] === $kId) {
                    return new Key($this->converter->toPEM($data));
                }
            }

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Error during fetching JWK for url %s', $jwksUrl),
                $exception->getCode(),
                $exception
            );
        }

        throw new LtiException(sprintf('Cannot fetch JWK for url %s', $jwksUrl));
    }
}
