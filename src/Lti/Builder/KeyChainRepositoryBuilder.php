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

namespace App\Lti\Builder;

use App\Lti\Core\Security\Key\KeyChain;
use App\Lti\Core\Security\Key\KeyChainRepository;
use App\Lti\Core\Security\Key\KeyChainRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class KeyChainRepositoryBuilder
{
    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function build(): KeyChainRepositoryInterface
    {
        $repository = new KeyChainRepository();

        foreach ($this->parameterBag->get('keyChains') as $group => $keyChainsData) {
            foreach ($keyChainsData as $id => $data) {
                $repository->add(
                    new KeyChain(
                        $id,
                        $group,
                        $data['publicKey'],
                        $data['privateKey'] ?? null,
                        $data['privateKeyPassPhrase'] ?? null
                    )
                );
            }
        }

        return $repository;
    }
}
