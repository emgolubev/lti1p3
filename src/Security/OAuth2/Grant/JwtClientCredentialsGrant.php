<?php

declare(strict_types=1);

namespace OAT\Library\Lti1p3Core\Security\Oauth2;

use DateInterval;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use League\Event\EmitterAwareTrait;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtClientCredentialsGrant extends AbstractGrant
{
    use EmitterAwareTrait;
    use CryptTrait;

    /** @var DateInterval */
    protected $refreshTokenTTL;

    /** @var ClientRepositoryInterface */
    protected $clientRepository;

    /** @var AccessTokenRepositoryInterface */
    protected $accessTokenRepository;

    /** @var ScopeRepositoryInterface */
    protected $scopeRepository;

    protected $publicKey = null;

    public function __construct($publicKey = null)
    {
        $this->publicKey = $publicKey;
    }

    public function getIdentifier(): string
    {
        return 'client_credentials';
    }

    public function canRespondToAccessTokenRequest(ServerRequestInterface $request): bool
    {
        $body = (array) $request->getParsedBody();

        return
            array_key_exists('grant_type', $body)
            && $body['grant_type'] === $this->getIdentifier()
            && array_key_exists('client_assertion', $body)
            && $body['client_assertion_type'] === 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
    }

    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType, DateInterval $accessTokenTTL)
    {
        /**
         * TODO
         * - parse JWT token from $request -> 'client_assertion'
         * - get Deployment based on the issuer (iss)
         * - check JWT signature based on the key id (kid) by calling the proper JWKS endpoint
         * - validate iss, sub, exp, aud and jti
         * - issue new access token using access token repository
         */

        $body = (array) $request->getParsedBody();

        // Validate request
        $jws = $this->validateAssertion($request);
        $scopes = $this->validateScopes($body['scope'] ?? null);

        $client = $this->clientRepository->getClientEntity($jws['iss']);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client);

        // Issue and persist access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, null, $finalizedScopes);

        // Send event to emitter
        $this->getEmitter()->emit(new RequestEvent('access_token.issued', $request));

        // Inject access token into response type
        $responseType->setAccessToken($accessToken);

        return $responseType;
    }

    /**
     * @throws OAuthServerException
     */
    protected function validateAssertion(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();

        $assertion = $body['client_assertion'] ?? null;

        if (null === $assertion) {
            throw OAuthServerException::invalidRequest('client_assertion');
        }

        $token = (new Parser())->parse((string) $assertion);

        if (
            // validate timestamps at the moment
            false === $token->validate(new ValidationData())
            // if public key is set and verification fails
            || ($this->publicKey && false === $token->verify(new Sha256(), new Key($this->publicKey)))
        ) {
            throw OAuthServerException::invalidRequest('client_assertion', 'Provided JWT is not valid');
        }

        return $token->getClaims();
    }
}
