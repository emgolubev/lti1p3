<?php declare(strict_types=1);
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
namespace App\Tests\Unit\Lti\Core\Service;

use App\Lti\Core\Deployment\DeploymentInterface;
use App\Lti\Core\Deployment\DeploymentRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Signer;
use OAT\Library\Lti1p3Core\Service\ServiceConnector;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceConnectorTest extends KernelTestCase
{
    /** @var ServiceConnector */
    private $subject;

    /** @var DeploymentInterface */
    private $deployment;

    public function setUp(): void
    {
        self::bootKernel();

        $body = [
            'access_token' => 'dkj4985kjaIAJDJ89kl8rkn5',
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'scope' => 'https=>//purl.imsglobal.org/spec/lti-ags/scope/lineitem https=>//purl.imsglobal.org/spec/lti-ags/scope/result/read'
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($body))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $repository = static::$container->get(DeploymentRepository::class);
        $this->deployment = $repository->find('1');
        $this->subject = new ServiceConnector($client, static::$container->get(Signer::class), 3600);
    }

    public function doPlatformServiceTest(): void
    {
        $request = new Request('GET', []);


        $this->subject->doPlatformServiceRequest($request, $this->deployment);

    }

    public function doToolServiceRequestTest(): void
    {


    }
}
