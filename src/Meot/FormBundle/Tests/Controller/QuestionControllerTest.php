<?php

namespace Meot\FormBundle\Tests\Controller;

use Meot\FormBundle\Tests\FunctionalTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FOS\Rest\Util\Codes;

class QuestionControllerTest extends FunctionalTestCase
{
    public static function setUpBeforeClass()
    {
        static::initialize();
    }

    public function testGet()
    {
        $client = $this->getClient('user');

        $crawler = $client->request('GET', '/api/questions.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $result = json_decode($response->getContent());
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals(3, $result[2]->id);
    }

    public function testPost()
    {
        $client = $this->getClient('user');

        $crawler = $client->request(
            'POST', '/api/questions.json', array(), array(), array(
                'CONTENT_TYPE' => 'application/json',
            ),
            '{"question":{"text":"Test","response_type":"1","is_master":true}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        // check location
        $this->assertTrue(
            $response->headers->contains('Location', 'http://localhost/api/questions/4'),
            $response->headers
        );

        // verify against database
        $forms = $this->entityManager->getRepository('MeotFormBundle:Question')->findAll();
        $this->assertEquals(4, count($forms));

        // test missing field
        $crawler = $client->request(
            'POST', '/api/questions.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"response_type":"1","is_master":true}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetObject()
    {
        $client = $this->getClient('user');

        $crawler = $client->request('GET', '/api/questions/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $question = json_decode($response->getContent());

        $expected = $this->entityManager->find('MeotFormBundle:Question', 1);
        $this->assertEquals($expected->getId(), $question->id);
        $this->assertEquals($expected->getText(), $question->text);

        // get non existing object
        $crawler = $client->request('GET', '/api/questions/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    public function testPut()
    {
        $client = $this->getClient('user');

        $crawler = $client->request(
            'PUT', '/api/questions/2.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"text":"Question 2a","response_type":2,"is_public":true,"is_master":false,"metadata":{}}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NO_CONTENT);

        // verify against database
        $result = $this->entityManager->find('MeotFormBundle:Question', 2);
        $this->assertEquals(2, $result->getId());
        $this->assertEquals('Question 2a', $result->getText());
        // to set boolean to false, the field should be absent from json request
        $this->assertTrue($result->getIsPublic());
        $this->assertFalse($result->getIsMaster());
        $this->assertEquals(1, $result->getOwner(), 'Owner ID should not change');

        // update a non-existing object
        $client = static::getClient('user');
        $crawler = $client->request(
            'PUT', '/api/questions/999.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"text":"Question 2 updated", "response_type":1, "is_public":1, "owner":1}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);

        // update other's question
        $client = static::getClient('user');
        $crawler = $client->request(
            'PUT', '/api/questions/3.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"text":"Question 3", "response_type":1, "is_public":1, "owner":1}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_FORBIDDEN);

    }
}
