<?php

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Teamleader\Uuidifier\Uuidifier;

class UudifierTest extends TestCase
{
    /**
     * @test
     */
    public function itEncodesValidIds()
    {
        $generator = new Uuidifier();
        $uuid = $generator->encode('foo', 1);
        $this->assertInstanceOf(UuidInterface::class, $uuid);
    }

    /**
     * @test
     */
    public function itDecodesUuids()
    {
        $generator = new Uuidifier();

        for ($i = 0; $i < 100; $i++) {
            $id = rand(1, 10000000);
            $decoded = $generator->decode($generator->encode('foo', $id));
            $this->assertEquals($id, $decoded);
        }
    }

    /**
     * @test
     */
    public function itEmbedsTheVersionAndVariant()
    {
        for ($version = 0; $version < 10; $version++) {
            $generator = new Uuidifier($version);
            $uuid = $generator->encode('foo', rand(0, 10000000));
            $this->assertEquals($version, $uuid->getVersion());
            $this->assertEquals(Uuid::RFC_4122, $uuid->getVariant());
        }
    }

    /**
     * @test
     */
    public function itGeneratesDifferentUuidsForDifferentPrefixes()
    {
        $generator = new Uuidifier();
        $uuid1 = $generator->encode('foo', 1);
        $uuid2 = $generator->encode('bar', 1);
        $this->assertNotEquals($uuid1->toString(), $uuid2->toString());
    }

    /**
     * @test
     */
    public function uuidWithCorrectPrefixIsValid()
    {
        $generator = new Uuidifier();
        $uuid = $generator->encode('foo', 1);
        $this->assertTrue($generator->isValid('foo', $uuid));
    }

    /**
     * @test
     */
    public function uuidWithDifferentPrefixIsInvalid()
    {
        $generator = new Uuidifier();
        $uuid = $generator->encode('foo', 1);
        $this->assertFalse($generator->isValid('bar', $uuid));
    }

    /**
     * @test
     */
    public function uuidWithDifferentNumberIsInvalid()
    {
        $generator = new Uuidifier();
        $uuid1 = $generator->encode('foo', 1);
        $uuid2 = Uuid::fromString(rtrim($uuid1, '1') . '2');

        $this->assertNotEquals($uuid1, $uuid2);
        $this->assertTrue($generator->isValid('foo', $uuid1));
        $this->assertFalse($generator->isValid('foo', $uuid2));
    }
}
