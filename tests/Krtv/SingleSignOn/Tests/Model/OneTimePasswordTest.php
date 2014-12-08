<?php

namespace Krtv\SingleSignOn\Tests\Model;

use Krtv\SingleSignOn\Model\OneTimePassword;

/**
 * Class OneTimePasswordTest
 * @package Krtv\SingleSignOn\Tests\Model
 */
class OneTimePasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OneTimePassword
     */
    private $model;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->model = new OneTimePassword();
    }

    /**
     *
     */
    public function testId()
    {
        $this->assertNull($this->model->getId());
    }

    /**
     *
     */
    public function testPassword()
    {
        $this->assertNull($this->model->getPassword());

        $this->model->setPassword($password = uniqid());

        $this->assertEquals($password, $this->model->getPassword());
    }

    /**
     *
     */
    public function testHash()
    {
        $this->assertNull($this->model->getHash());

        $this->model->setHash($hash = uniqid());

        $this->assertEquals($hash, $this->model->getHash());
    }

    /**
     *
     */
    public function testUsed()
    {
        $this->assertNull($this->model->getUsed());

        $this->model->setUsed(true);

        $this->assertTrue($this->model->getUsed());

        $this->model->setUsed(false);

        $this->assertFalse($this->model->getUsed());
    }

    /**
     *
     */
    public function testCreated()
    {
        $this->assertNull($this->model->getCreated());

        $this->model->setCreated($created = new \DateTime());

        $this->assertEquals($created, $this->model->getCreated());
    }
}
