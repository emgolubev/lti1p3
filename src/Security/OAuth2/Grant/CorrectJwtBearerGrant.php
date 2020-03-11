<?php


namespace App\Security\OAuth2\Grant;


use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\KeyManagement\JWKFactory;
use League\OAuth2\Server\Exception\OAuthServerException;
use MilesChou\OAuth2\JwtBearerGrant;
use Psr\Http\Message\ServerRequestInterface;

class CorrectJwtBearerGrant extends JwtBearerGrant
{
    public function getIdentifier(): string
    {
        return 'client_credentials';
    }

    protected function validateAssertion(ServerRequestInterface $request)
    {
        // If the client is confidential require the client secret
        $assertion = $this->getRequestParameter('client_assertion', $request);

        if (null === $assertion) {
            throw OAuthServerException::invalidRequest('client_assertion');
        }

        $jwt = $this->resolveJwsSerializerManager()->unserialize($assertion);

        $jsonConverter = new StandardConverter();

        $claims = $jsonConverter->decode($jwt->getPayload());

//        $this->resolveClaimCheckerManager()->check($claims);

        return $claims;
    }
}