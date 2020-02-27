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

namespace App\Lti\Core\Message\Context;

class SecurityContext
{
    /** @var string */
    private $alg;

    /** @var string */
    private $kid;

    /** @var string */
    private $iss;

    /** @var string */
    private $sub;

    /** @var string */
    private $aud;

    /** @var int */
    private $exp;

    /** @var int */
    private $iat;

    /** @var string */
    private $nonce;

    public function __construct(
        string $alg,
        string $kid,
        string $iss,
        string $sub,
        string $aud,
        int $exp,
        int $iat,
        string $nonce
    ){
        $this->alg = $alg;
        $this->kid = $kid;
        $this->iss = $iss;
        $this->sub = $sub;
        $this->aud = $aud;
        $this->exp = $exp;
        $this->iat = $iat;
        $this->nonce = $nonce;
    }

    public function getAlg(): string
    {
        return $this->alg;
    }

    public function getKid(): string
    {
        return $this->kid;
    }

    public function getIss(): string
    {
        return $this->iss;
    }

    public function getSub(): string
    {
        return $this->sub;
    }

    public function getAud(): string
    {
        return $this->aud;
    }

    public function getExp(): int
    {
        return $this->exp;
    }

    public function getIat(): int
    {
        return $this->iat;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }
}
