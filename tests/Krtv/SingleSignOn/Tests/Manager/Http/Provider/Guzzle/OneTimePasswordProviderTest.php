<?php

namespace Krtv\SingleSignOn\Tests\Manager\Http\Provider\Guzzle;

use Guzzle\Http\Exception\BadResponseException;
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

        $responseMock = $this->getResponseMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($json);

        $requestMock = $this->getRequestMock();
        $requestMock->expects($this->once())
            ->method('send')
            ->willReturn($responseMock);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('get')
            ->withConsecutive(
                array('?_otp=12345', null, array())
            )
            ->willReturn($requestMock);

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
    public function testGatewayException()
    {
        $exception = new BadResponseException();

        $requestMock = $this->getRequestMock();
        $requestMock->expects($this->once())
            ->method('send')
            ->willThrowException($exception);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('get')
            ->withConsecutive(
                array('?_otp=12345', null, array())
            )
            ->willReturn($requestMock);

        $provider = new OneTimePasswordProvider($clientMock, '/api/v1/otp', new NullLogger());

        $otp = $provider->fetch('12345');

        $this->assertNull($otp);
    }

    /**
     *
     */
    public function testRuntimeException()
    {
        $exception = new \Exception();

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $provider = new OneTimePasswordProvider($clientMock, '/api/v1/otp', new NullLogger());

        $this->setExpectedException('Exception');

        $otp = $provider->fetch('12345');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock()
    {
        return $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getBody'))
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