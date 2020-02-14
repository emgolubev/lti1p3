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

namespace App\Lti\Core;

use DateTimeInterface;

class LineItem
{
    /** @var string */
    private $id;

    /** @var float */
    private $scoreMaximum;

    /** @var string|null */
    private $resourceLinkId;

    /** @var string|null */
    private $resourceId;

    /** @var string|null */
    private $label;

    /** @var string|null */
    private $tag;

    /** @var DateTimeInterface|null */
    private $startDateTime;

    /** @var DateTimeInterface|null */
    private $endDateTime;

    public function __construct(
        string $id,
        float $scoreMaximum,
        string $resourceLinkId = null,
        string $resourceId = null,
        string $label = null,
        string $tag = null,
        DateTimeInterface $startDateTime = null,
        DateTimeInterface $endDateTime = null
    ) {
        $this->id = $id;
        $this->scoreMaximum = $scoreMaximum;
        $this->resourceLinkId = $resourceLinkId;
        $this->resourceId = $resourceId;
        $this->label = $label;
        $this->tag = $tag;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getScoreMaximum(): float
    {
        return $this->scoreMaximum;
    }

    public function setScoreMaximum(float $scoreMaximum): self
    {
        $this->scoreMaximum = $scoreMaximum;

        return $this;
    }

    public function getResourceLinkId(): ?string
    {
        return $this->resourceLinkId;
    }


    public function setResourceLinkId(?string $resourceLinkId): self
    {
        $this->resourceLinkId = $resourceLinkId;

        return $this;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(?string $resourceId): self
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getStartDateTime(): ?DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(?DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getEndDateTime(): ?DateTimeInterface
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(?DateTimeInterface $endDateTime): self
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }
}
