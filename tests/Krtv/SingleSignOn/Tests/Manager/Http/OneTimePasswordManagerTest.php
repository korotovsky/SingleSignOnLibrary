<?php

namespace Krtv\SingleSignOn\Tests\Manager\Http;

use Krtv\SingleSignOn\Manager\Http\OneTimePasswordManager;

/**
 * Class OneTimePasswordManagerTest
 * @package Krtv\SingleSignOn\Tests\Manager\Http
 */
class OneTimePasswordManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCreate()
    {
        $manager = new OneTimePasswordManager($this->getProviderMock());

        $this->setExpectedException('BadMethodCallException', 'Service Provider can\'t create OTP tokens.');

        $manager->create('12345');
    }

    /**
     *
     */
    public function testGet()
    {
        $otp = $this->getOtp();
        $otp->expects($this->once())
            ->method('getPassword')
            ->willReturn('12345');

        $providerMock = $this->getProviderMock();
        $providerMock->expects($this->once())
            ->method('fetch')
            ->withConsecutive(
                array('12345')
            )
            ->willReturn($otp);

        $manager = new OneTimePasswordManager($providerMock);

        $otp = $manager->get('12345');

        $this->assertNotNull($otp);
        $this->assertEquals('12345', $otp->getPassword());
    }

    /**
     *
     */
    public function testIsValid()
    {
        $otp = $this->getOtp();
        $otp->expects($this->once())
            ->method('getUsed')
            ->willReturn(false);

        $manager = new OneTimePasswordManager($this->getProviderMock());

        $actual = $manager->isValid($otp);

        $this->assertTrue($actual);
    }

    /**
     *
     */
    public function testInvalidate()
    {
        $manager = new OneTimePasswordManager($this->getProviderMock());
        $manager->invalidate($this->getMock('Krtv\SingleSignOn\Model\OneTimePasswordInterface'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getProviderMock()
    {
        return $this->getMockBuilder('Krtv\SingleSignOn\Manager\Http\Provider\ProviderInterface')
            ->setMethods(array())
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getOtp()
    {
        return $this->getMockBuilder('Krtv\SingleSignOn\Model\OneTimePasswordInterface')
            ->setMethods(array())
            ->getMock();
    }
} 