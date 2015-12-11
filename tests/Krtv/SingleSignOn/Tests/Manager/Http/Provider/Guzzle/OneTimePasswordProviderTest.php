<?php

namespace Krtv\SingleSignOn\Tests\Manager\Http\Provider\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Krtv\SingleSignOn\Manager\Http\Provider\Guzzle\OneTimePasswordProvider;
use Psr\Log\NullLogger;

/**
 * Class OneTimePasswordProviderTest
 * @package Krtv\SingleSignOn\Tests\Manager\Http\Provider\Guzzle
 */
class OneTimePasswordProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testFetch()
    {
        $json = '{"data": {"created_at": "2015-01-01T10:00:00+00:00", "hash": "ABCD", "password": "12345", "is_used": false}}';

        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);

        $clientMock = $this->getClientMock(HandlerStack::create($mock));

        $provider = new OneTimePasswordProvider($clientMock, '/api/v1/otp', new NullLogger());

        $otp = $provider->fetch('12345');

        $this->assertNotNull($otp);
        $this->assertEquals('12345', $otp->getPassword());
        $this->assertEquals('ABCD', $otp->getHash());
        $this->assertEquals(false, $otp->getUsed());
        $this->assertEquals(new \DateTime('2015-01-01T10:00:00+00:00'), $otp->getCreated());
    }

    /**
     *
     */
    public function testFetchWithInvalidResponse()
    {
        $json = '{"data": }';

        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);

        $clientMock = $this->getClientMock(HandlerStack::create($mock));

        $provider = new OneTimePasswordProvider($clientMock, '/api/v1/otp', new NullLogger());

        $otp = $provider->fetch('12345');

        $this->assertNull($otp);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock($handler)
    {
        return new Client(['handler' => $handler]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getBody', 'getMessage'))
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestMock()
    {
        return $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
    }
} 