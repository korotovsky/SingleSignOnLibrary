<?php

namespace Krtv\SingleSignOn\Tests\Manager\ORM;

use Doctrine\DBAL\DBALException;
use Krtv\SingleSignOn\Manager\ORM\OneTimePasswordManager;

/**
 * Class OneTimePasswordManagerTest
 * @package Krtv\SingleSignOn\Tests\Manager\ORM
 */
class OneTimePasswordManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCreate()
    {
        $repositoryMock = $this->getRepositoryMock();
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->withConsecutive(
                array(array(
                    'hash' => '12345',
                ))
            )
            ->willReturn(null);

        $entityManagerMock = $this->getEntityManagerMock();
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);


        $manager = new OneTimePasswordManager($entityManagerMock, 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');

        $password = $manager->create('12345');

        $this->assertNotNull($password);
    }

    /**
     *
     */
    public function testCreateHashCollision()
    {
        $otp = new OneTimePassword();
        $otp->setCreated(new \DateTime());
        $otp->setHash('ABCD');
        $otp->setPassword('12345');
        $otp->setUsed(false);

        $repositoryMock = $this->getRepositoryMock();
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->withConsecutive(
                array(array(
                    'hash' => '12345',
                ))
            )
            ->willReturn($otp);

        $entityManagerMock = $this->getEntityManagerMock();
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $this->setExpectedException('Exception', 'A one-time-password for hash "12345" already exists');

        $manager = new OneTimePasswordManager($entityManagerMock, 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');
        $manager->create('12345');
    }

    /**
     *
     */
    public function testCreateDBALException()
    {
        $exception = new DBALException();

        $repositoryMock = $this->getRepositoryMock();
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->withConsecutive(
                array(array(
                    'hash' => '12345',
                ))
            )
            ->willReturn(null);

        $entityManagerMock = $this->getEntityManagerMock();
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $entityManagerMock->expects($this->once())
            ->method('flush')
            ->willThrowException($exception);

        $this->setExpectedException('Exception', 'Could not create a one-time-password');

        $manager = new OneTimePasswordManager($entityManagerMock, 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');
        $manager->create('12345');
    }

    /**
     *
     */
    public function testGet()
    {
        $otp = new OneTimePassword();
        $otp->setCreated(new \DateTime());
        $otp->setHash('ABCD');
        $otp->setPassword('12345');
        $otp->setUsed(false);

        $repositoryMock = $this->getRepositoryMock();
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->withConsecutive(
                array(array(
                    'password' => '12345',
                ))
            )
            ->willReturn($otp);

        $entityManagerMock = $this->getEntityManagerMock();
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);


        $manager = new OneTimePasswordManager($entityManagerMock, 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');

        $otp = $manager->get('12345');

        $this->assertNotNull($otp);
        $this->assertInstanceOf('Krtv\SingleSignOn\Model\OneTimePasswordInterface', $otp);
    }

    /**
     *
     */
    public function testIsValid()
    {
        $otp = new OneTimePassword();
        $otp->setCreated(new \DateTime());
        $otp->setHash('ABCD');
        $otp->setPassword('12345');
        $otp->setUsed(false);

        $manager = new OneTimePasswordManager($this->getEntityManagerMock(), 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');

        $actual = $manager->isValid($otp);

        $this->assertTrue($actual);

        $otp->setUsed(true);

        $actual = $manager->isValid($otp);

        $this->assertFalse($actual);
    }

    /**
     *
     */
    public function testInvalidate()
    {
        $otp = new OneTimePassword();
        $otp->setCreated(new \DateTime());
        $otp->setHash('ABCD');
        $otp->setPassword('12345');
        $otp->setUsed(false);

        $manager = new OneTimePasswordManager($this->getEntityManagerMock(), 'Krtv\SingleSignOn\Tests\Manager\ORM\OneTimePassword');
        $manager->invalidate($otp);

        $this->assertTrue($otp->getUsed());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityManagerMock()
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRepositoryMock()
    {
        return $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
    }
}

/**
 * Class OneTimePassword
 * @package Krtv\SingleSignOn\Tests\Manager\ORM
 *
 * @ORM\Entity
 */
class OneTimePassword extends \Krtv\SingleSignOn\Model\OneTimePassword
{

}