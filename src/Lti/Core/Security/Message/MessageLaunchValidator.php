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

namespace App\Lti\Core\Security\Message;

use App\Lti\Core\Exception\LtiException;
use App\Lti\Core\Message\MessageLaunchInterface;
use App\Lti\Core\Security\Jwks\JwksFetcherInterface;
use App\Lti\Core\Security\Nonce\NonceRepositoryInterface;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Throwable;

class MessageLaunchValidator
{
    /** @var Signer */
    private $signer;

    /** @var Parser */
    private $parser;

    /** @var JwksFetcherInterface */
    private $jwksFetcher;

    /** @var NonceRepositoryInterface */
    private $nonceRepository;

    public function __construct(
        Signer $signer,
        Parser $parser,
        JwksFetcherInterface $jwksFetcher,
        NonceRepositoryInterface $nonceRepository
    ) {
        $this->signer = $signer;
        $this->parser = $parser;
        $this->jwksFetcher = $jwksFetcher;
        $this->nonceRepository = $nonceRepository;
    }

    /**
     * @throws LtiException
     */
    public function validate(MessageLaunchInterface $launch): MessageLaunchValidationResult
    {
        $result = new MessageLaunchValidationResult();

        try {
            $this
                ->validateTokenSignature($launch, $result)
                ->validateTokenClaims($launch, $result)
                ->validateTokenExpiry($launch, $result)
                ->validateTokenNonce($launch, $result)
                ->validateStateSignature($launch, $result)
                ->validateStateExpiry($launch, $result)
                ->validateIssuer($launch, $result)
                ->validateAudience($launch, $result);

            return $result;

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('LTI message validation failed: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws LtiException
     */
    private function validateTokenSignature(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        $key = $this->jwksFetcher->fetchKey(
            $launch->getDeployment()->getPlatformContext()->getJwksUrl(),
            $launch->getToken()->getHeader(MessageLaunchInterface::HEADER_KID)
        );

        if(!$launch->getToken()->verify($this->signer, $key)) {
            $result->addFailure('JWT token signature validation failure');
        } else {
            $result->addSuccess('JWT token signature validation success');
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateTokenClaims(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        $mandatoryClaims = [
            MessageLaunchInterface::CLAIM_ISS,
            MessageLaunchInterface::CLAIM_AUD,
            MessageLaunchInterface::CLAIM_SUB,
            MessageLaunchInterface::CLAIM_EXP,
            MessageLaunchInterface::CLAIM_IAT,
            MessageLaunchInterface::CLAIM_NONCE,
        ];

        $didFail = false;
        foreach ($mandatoryClaims as $mandatoryClaim) {
            if(!$launch->getToken()->hasClaim($mandatoryClaim)) {
                $result->addFailure(sprintf('Missing mandatory token JWT claim %s', $mandatoryClaim));
                $didFail = true;
            }
        }

        if (!$didFail) {
            $result->addSuccess('JWT token mandatory claims are all provided');
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateStateSignature(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        if (null !== $launch->getState()) {

            $key = $launch->getDeployment()->getToolContext()->getKeyChain()->getPublicKey();

            if(!$launch->getState()->verify($this->signer,  $key)) {
                $result->addFailure('JWT state signature validation failure');
            } else {
                $result->addSuccess('JWT state signature validation success');
            }
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateTokenExpiry(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        if ($launch->getToken()->isExpired()) {
            $result->addFailure('JWT token is expired');
        } else {
            $result->addSuccess('JWT token is not expired');
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateStateExpiry(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        if (null !== $launch->getState()) {
            if ($launch->getState()->isExpired()) {
                $result->addFailure('JWT state is expired');
            } else {
                $result->addSuccess('JWT state is not expired');
            }
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateTokenNonce(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        $nonce = $this->nonceRepository->find(
            $launch->getToken()->getClaim(MessageLaunchInterface::CLAIM_NONCE)
        );

        if (null !== $nonce) {
            if (!$nonce->isExpired()) {
                $result->addFailure('JWT token nonce already used');
            } else {
                $result->addSuccess('JWT token nonce is valid');
            }
        } else {
            $result->addSuccess('JWT token nonce is valid');
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateIssuer(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        if ($launch->getDeployment()->getPlatform()->getAudience() != $launch->getToken()->getClaim(MessageLaunchInterface::CLAIM_ISS)) {
            $result->addFailure('JWT token iss claim does not match platform audience');
        } else {
            $result->addSuccess('JWT token iss claim matches platform audience');
        }

        return $this;
    }

    /**
     * @throws LtiException
     */
    private function validateAudience(MessageLaunchInterface $launch, MessageLaunchValidationResult $result): self
    {
        if ($launch->getDeployment()->getOAuth2ClientId() != $launch->getToken()->getClaim(MessageLaunchInterface::CLAIM_AUD)) {
            $result->addFailure('JWT token aud claim does not match tool oauth2 client id');
        } else {
            $result->addSuccess('JWT token aud claim issuer matches tool oauth2 client id');
        }

        return $this;
    }
}
