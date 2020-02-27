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

namespace App\Lti\Core\Deployment;

use App\Lti\Core\Platform\PlatformInterface;
use App\Lti\Core\Tool\ToolInterface;

class Deployment implements DeploymentInterface
{
    /** @var string */
    private $id;

    /** @var PlatformInterface */
    private $platform;

    /** @var ToolInterface */
    private $tool;

    /** @var DeploymentContextInterface */
    private $platformContext;

    /** @var DeploymentContextInterface */
    private $toolContext;

    public function __construct(
        string $id,
        PlatformInterface $platform,
        ToolInterface $tool,
        DeploymentContextInterface $platformContext,
        DeploymentContextInterface $toolContext
    ) {
        $this->id = $id;
        $this->platform = $platform;
        $this->tool = $tool;
        $this->platformContext = $platformContext;
        $this->toolContext = $toolContext;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPlatform(): PlatformInterface
    {
        return $this->platform;
    }

    public function getTool(): ToolInterface
    {
        return $this->tool;
    }

    public function getPlatformContext(): DeploymentContextInterface
    {
        return $this->platformContext;
    }

    public function getToolContext(): DeploymentContextInterface
    {
        return $this->toolContext;
    }
}
