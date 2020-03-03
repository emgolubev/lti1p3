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

use App\Lti\Core\Tool\Tool;
use App\Lti\Core\Tool\ToolRepository;
use App\Lti\Core\Tool\ToolRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ToolRepositoryBuilder
{
    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function build(): ToolRepositoryInterface
    {
        $repository = new ToolRepository();

        foreach ($this->parameterBag->get('tools') as $id => $data) {
            $repository->add(
                new Tool(
                    $id,
                    $data['name'],
                    $data['deepLaunchUrl'],
                    $data['oidcLoginInitiationUrl']
                )
            );
        }

        return $repository;
    }
}
