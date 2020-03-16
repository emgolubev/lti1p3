<?php

declare(strict_types = 1);

namespace OAT\Library\Lti1p3Core\Service;

use App\Lti\Core\Deployment\DeploymentInterface;
use App\Lti\Core\Exception\LtiException;
use App\Lti\Core\Security\Key\KeyChainInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

class ServiceConnector
{
    /** @var Client */
    private $client;

    /** @var Signer */
    private $signer;

    /** @var int */
    private $ttl;

    public function __construct(Client $client, Signer $signer, int $ttl)
    {
        $this->client = $client;
        $this->signer = $signer;
        $this->ttl = $ttl;
    }

    public function doToolServiceRequest(
        RequestInterface $request,
        DeploymentInterface $deployment,
        array $options  = [],
        array $scopes = []
    ): ResponseInterface {
        $request->withHeader('Authorization', $this->getToolAccessToken($deployment, $scopes));

        return $this->client->send($request, $options);
    }

    public function doPlatformServiceRequest(
        RequestInterface $request,
        DeploymentInterface $deployment,
        array $options  = [],
        array $scopes = []
    ): ResponseInterface {
        $request->withHeader('Authorization', $this->getPlatformAccessToken($deployment, $scopes));

        return $this->client->send($request, $options);
    }

    private function getToolAccessToken(DeploymentInterface $deployment, $scopes = []): string
    {
        $response = $this->client->request('POST', $deployment->getTool()->getOAuth2AccessTokenUrl(), [
            'json' => [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => (string) $this->createClientCredentialsJwt(
                    $deployment->getPlatform()->getName(),
                    $deployment->getClientId(),
                    $deployment->getTool()->getOAuth2AccessTokenUrl(),
                    $deployment->getPlatformKeyPair()
                ),
                'scope' => implode(' ', $scopes),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new LtiException('failed'); // TODO
        }

        $body = json_decode($response->getBody()->getContents(), true);

        return ucfirst($body['token_type']) . ' ' . $body['access_token'];
    }

    private function getPlatformAccessToken(DeploymentInterface $deployment, $scopes = []): string
    {
        $response = $this->client->request('POST', $deployment->getPlatform()->getOAuth2AccessTokenUrl(), [
            'json' => [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => (string) $this->createClientCredentialsJwt(
                    $deployment->getTool()->getName(),
                    $deployment->getClientId(),
                    $deployment->getPlatform()->getOAuth2AccessTokenUrl(),
                    $deployment->getToolKeyPair()
                ),
                'scope' => implode(' ', $scopes),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new LtiException('failed'); // TODO
        }

        $body = json_decode($response->getBody()->getContents(), true);

        return ucfirst($body['token_type']) . ' ' . $body['access_token'];
    }

    private function createClientCredentialsJwt(string $iss, string $sub, string $aud, KeyChainInterface $keyChain): Token
    {
        $timestamp = Carbon::now()->getTimestamp();

        return (new Builder())
            ->withHeader('kid', $keyChain->getId())
            ->issuedBy($iss)
            ->relatedTo($sub)
            ->permittedFor($aud)
            ->issuedAt($timestamp)
            ->expiresAt($timestamp + $this->ttl)
            ->identifiedBy(Uuid::uuid4()->toString())
            ->getToken($this->signer, $keyChain->getPrivateKey());
    }
}
