<?php

namespace Alchemy\Tests\Phrasea\Controller\Prod;

/**
 * @group functional
 * @group legacy
 * @group authenticated
 * @group web
 */
class EditTest extends \PhraseanetAuthenticatedWebTestCase
{
    protected $client;

    /**
     * Default route test
     */
    public function testRouteSlash()
    {
        self::$DI['client']->request('POST', '/prod/records/edit/', ['lst' => self::$DI['record_1']->get_serialize_key()]);

        $response = self::$DI['client']->getResponse();

        $this->assertTrue($response->isOk());
    }

    public function testRouteSlashWithStory()
    {
        self::$DI['client']->request('POST', '/prod/records/edit/', ['lst' => self::$DI['record_story_1']->get_serialize_key()]);

        $response = self::$DI['client']->getResponse();

        $this->assertTrue($response->isOk());
    }

    public function testApply()
    {
        self::$DI['client']->request('POST', '/prod/records/edit/apply/', ['lst' => self::$DI['record_1']->get_serialize_key()]);

        $response = self::$DI['client']->getResponse();

        $this->assertTrue($response->isOk());
    }

    public function testVocabulary()
    {
        self::$DI['client']->request('GET', '/prod/records/edit/vocabulary/Zanzibar/');

        $response = self::$DI['client']->getResponse();
        $this->assertTrue($response->isOk());
        $datas = json_decode($response->getContent());
        $this->assertFalse($datas->success);

        self::$DI['client']->request('GET', '/prod/records/edit/vocabulary/User/');

        $response = self::$DI['client']->getResponse();
        $this->assertTrue($response->isOk());
        $datas = json_decode($response->getContent());
        $this->assertFalse($datas->success);

        $params = ['sbas_id' => self::$DI['collection']->get_sbas_id()];
        self::$DI['client']->request('GET', '/prod/records/edit/vocabulary/Zanzibar/', $params);

        $response = self::$DI['client']->getResponse();
        $this->assertTrue($response->isOk());
        $datas = json_decode($response->getContent());
        $this->assertFalse($datas->success);

        $params = ['sbas_id' => self::$DI['collection']->get_sbas_id()];
        self::$DI['client']->request('GET', '/prod/records/edit/vocabulary/User/', $params);

        $response = self::$DI['client']->getResponse();
        $this->assertTrue($response->isOk());
        $datas = json_decode($response->getContent());
        $this->assertTrue($datas->success);
    }
}
