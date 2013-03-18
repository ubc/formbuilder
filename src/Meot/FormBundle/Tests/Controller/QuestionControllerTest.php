<?php

namespace Meot\FormBundle\Tests\Controller;

use Meot\FormBundle\Tests\FunctionalTestCase;

class QuestionControllerTest extends FunctionalTestCase
{
    public static function setUpBeforeClass()
    {
        static::initialize();
    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/questions.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $result = json_decode($response->getContent());
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals(3, $result[2]->id);
    }
}
