<?php

declare(strict_types=1);

namespace App\Security\OAuth2\Token;

use App\Security\OAuth2\Entity\OAuth2Client;
use App\Security\OAuth2\Entity\OAuth2Scope;
use Carbon\Carbon;
use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

class OAuth2AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;

    /** @var string */
    protected $identifier;

    /** @var OAuth2Scope[] */
    protected $scopes = [];

    /** @var DateTimeImmutable */
    protected $expiryDateTime;

    /** @var string|int|null */
    protected $userIdentifier;

    /** @var OAuth2Client */
    protected $client;

    public function getId(): string
    {
        return $this->getIdentifier();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getScopes(): array
    {
        return array_values($this->scopes);
    }

    public function addScope(ScopeEntityInterface $scope): self
    {
        $this->scopes[$scope->getIdentifier()] = $scope;

        return $this;
    }

    public function getExpiryDateTime(): ?DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    public function setExpiryDateTime(DateTimeImmutable $dateTime)
    {
        $this->expiryDateTime = $dateTime;
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expiryDateTime);
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier($identifier): self
    {
        $this->userIdentifier = $identifier;

        return $this;
    }

    public function getClient(): ?ClientEntityInterface
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

}
