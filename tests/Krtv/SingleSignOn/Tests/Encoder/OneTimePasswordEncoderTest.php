<?php

namespace Krtv\SingleSignOn\Tests;
use Krtv\SingleSignOn\Encoder\OneTimePasswordEncoder;

/**
 * Class OneTimePasswordEncoder
 * @package Krtv\SingleSignOn\Tests
 */
class OneTimePasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OneTimePasswordEncoder
     */
    private $encoder;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->encoder = new OneTimePasswordEncoder('very_secret');
    }

    /**
     *
     */
    public function testEncodeHash()
    {
        $hashed = $this->encoder->encodeHash(array('username', $time = time()));

        $this->assertNotNull($hashed);
        $this->assertInternalType('string', $hashed);
    }

    /**
     *
     */
    public function testDecodeHash()
    {
        // username:1418036230
        $hash = 'dXNlcm5hbWU6MTQxODAzNjIzMA==';
        $parts = $this->encoder->decodeHash($hash);

        $this->assertNotNull($parts);
        $this->assertInternalType('array', $parts);
        $this->assertCount(2, $parts);
        $this->assertEquals('username', $parts[0]);
        $this->assertEquals('1418036230', $parts[1]);
    }

    /**
     *
     */
    public function testGenerateHash()
    {
        $hash = $this->encoder->generateHash('username', $time = time());

        $this->assertNotNull($hash);
        $this->assertEquals(64, strlen($hash));
    }

    /**
     *
     */
    public function testCompareHashes()
    {
        $hash1 = $this->encoder->generateHash('username', $time = time());
        $hash2 = $this->encoder->generateHash('username', $time) . 'APPENDIX';

        $this->assertFalse($this->encoder->compareHashes($hash1, $hash2));

        $hash1 = $this->encoder->generateHash('username', $time = time());
        $hash2 = $this->encoder->generateHash('username', $time);

        $this->assertTrue($this->encoder->compareHashes($hash1, $hash2));
    }

    /**
     *
     */
    public function testGenerateOneTimePasswordValue()
    {
        $hash = $this->encoder->generateOneTimePasswordValue('username', $time = time());

        $this->assertNotNull($hash);
        $this->assertInternalType('string', $hash);

        $parts = $this->encoder->decodeHash($hash);

        $this->assertNotNull($parts);
        $this->assertCount(3, $parts);

        $this->assertEquals('dXNlcm5hbWU=', $parts[0]);
        $this->assertEquals((string)$time, $parts[1]);
        $this->assertNotNull($parts[2]);
        $this->assertEquals(64, strlen($parts[2]));
    }
}
