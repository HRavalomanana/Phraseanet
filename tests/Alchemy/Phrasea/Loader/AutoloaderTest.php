<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2010 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package
 * @license     http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link        www.phraseanet.com
 */
require_once __DIR__ . '/../../../PhraseanetPHPUnitAbstract.class.inc';

class AutoloaderTest extends \PhraseanetPHPUnitAbstract
{

  public function testFindFile()
  {
    $testClassName = 'Test_Hello';
    $autoloader = new Alchemy\Phrasea\Loader\Autoloader();
    $autoloader->addPath('fixture', __DIR__ . '/Fixtures');
    $autoloader->loadClass($testClassName);
    $this->assertTrue(class_exists($testClassName));
  }

  public function testAddPath()
  {
    $autoloader = new Alchemy\Phrasea\Loader\Autoloader();
    $pathNb = count($autoloader->getPaths());
    $autoloader->addPath('fixture', __DIR__ . '/Fixtures');
    $this->assertGreaterThan($pathNb, count($autoloader->getPaths()));
    $this->assertArrayHasKey('fixture', $autoloader->getPaths());
  }

  public function testGetPath()
  {
    $autoloader = new Alchemy\Phrasea\Loader\Autoloader();
    $this->assertTrue(is_array($autoloader->getPaths()));
    $this->assertTrue(2 === count($autoloader->getPaths()));
    $this->assertArrayHasKey('config', $autoloader->getPaths());
    $this->assertArrayHasKey('library', $autoloader->getPaths());
  }
}
