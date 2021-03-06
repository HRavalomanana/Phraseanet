<?php

namespace Alchemy\Tests\Phrasea\Border\Checker;

use Alchemy\Phrasea\Border\File;
use Alchemy\Phrasea\Border\Checker\MediaType;

/**
 * @group functional
 * @group legacy
 */
class MediaTypeTest extends \PhraseanetTestCase
{
    /**
     * @var MediaType
     */
    protected $object;

    /**
     * @covers Alchemy\Phrasea\Border\Checker\CheckerInterface
     * @covers Alchemy\Phrasea\Border\Checker\MediaType::__construct
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new MediaType(self::$DI['app'], ['mediatypes' => [MediaType::TYPE_IMAGE]]);
    }

    /**
     * @covers Alchemy\Phrasea\Border\Checker\MediaType::check
     */
    public function testCheck()
    {
        $media = self::$DI['app']['mediavorus']->guess(__DIR__ . '/../../../../../files/test001.jpg');
        $file = new File(self::$DI['app'], $media, self::$DI['collection']);
        $response = $this->object->check(self::$DI['app']['orm.em'], $file);

        $this->assertTrue($response->isOk());

        $object = new MediaType(self::$DI['app'], ['mediatypes' => [MediaType::TYPE_VIDEO, MediaType::TYPE_AUDIO]]);

        $media = self::$DI['app']['mediavorus']->guess(__DIR__ . '/../../../../../files/test001.jpg');
        $file = new File(self::$DI['app'], $media, self::$DI['collection']);
        $response = $object->check(self::$DI['app']['orm.em'], $file);

        $this->assertFalse($response->isOk());
    }

    /**
     * @covers Alchemy\Phrasea\Border\Checker\MediaType::getMessage
     */
    public function testGetMessage()
    {
        $this->assertInternalType('string', $this->object->getMessage($this->createTranslatorMock()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testContructorInvalidArgumentException()
    {
        new MediaType(self::$DI['app'], [[MediaType::TYPE_IMAGE]]);
    }
}
