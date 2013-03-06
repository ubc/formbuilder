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

        $questions = json_decode($response->getContent());
        $this->assertEquals(3, count($questions));
        $this->assertEquals(1, $questions[0]->id);
        $this->assertEquals(2, $questions[1]->id);
        $this->assertEquals(3, $questions[2]->id);

    }

    public function testPost()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/questions.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"text":"Question 4", "response_type":1, "is_public":0, "is_master":0, "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 201);

        // verify against database
        $questions = $this->entityManager->getRepository('MeotFormBundle:Question')->findAll();
        $this->assertEquals(4, count($questions));

        // test missing field
        $crawler = $client->request(
            'POST', '/questions.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"response_type":1, "is_public":0, "is_master":0, "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 500);
    }

    public function testGetObject()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/questions/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $question = json_decode($response->getContent());

        $expected = $this->entityManager->find('MeotFormBundle:Question', 1);
        $this->assertEquals($expected->getId(), $question->id);
        $this->assertEquals($expected->getText(), $question->text);

        // get non existing object
        $crawler = $client->request('GET', '/questions/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    public function testPut()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'PUT', '/questions/2.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"text":"Question 2 updated", "response_type":1, "is_public":1, "owner":1}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 204);

        // verify against database
        $result = $this->entityManager->find('MeotFormBundle:Question', 2);
        $this->assertEquals(2, $result->getId());
        $this->assertEquals("Question 2 updated", $result->getText());
        // to set boolean to false, the field should be absent from json request
        $this->assertFalse($result->getIsMaster());

        // update a non-existing object
        $client = static::createClient();
        $crawler = $client->request(
            'PUT', '/questions/999.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"text":"Question 2 updated", "response_type":1, "is_public":1, "owner":1}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'DELETE', '/questions/3.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 204);

        $result = $this->entityManager->find('MeotFormBundle:Question', 3);
        $this->assertEquals(null, $result);

        // delete non-existing object
        $crawler = $client->request(
            'DELETE', '/questions/999.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    public function testResponseGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/questions/1/responses.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $result = json_decode($response->getContent());
        $this->assertEquals(3, count($result));
        $this->assertEquals('Response 1', $result[0]->text);
        $this->assertEquals('Response 2', $result[1]->text);
        $this->assertEquals('Response 3', $result[2]->text);

        // try to get responses for non-existing question
        $crawler = $client->request('GET', '/questions/999/responses.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

    }

    public function testResponsePost()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/questions/2/responses.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"response":{"text":"Response 10", "question":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 201);

        // verify against database
        $result = $this->entityManager->getRepository('MeotFormBundle:Response')->findByQuestion(2);
        $this->assertEquals(3, count($result));

        // test post response to non-existing question
        $crawler = $client->request(
            'POST', '/questions/999/responses.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"response":{"text":"Response 10", "question":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
        // response should not be inserted
        $result = $this->entityManager->getRepository('MeotFormBundle:Response')->findByQuestion(999);
        $this->assertEquals(array(), $result);
    }

    public function testResponseGetObject()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/questions/1/responses/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $result = json_decode($response->getContent());

        $expected = $this->entityManager->find('MeotFormBundle:Response', 1);
        $this->assertEquals($expected->getId(), $result->id);
        $this->assertEquals($expected->getText(), $result->text);
        $this->assertEquals($expected->getQuestion()->getId(), $result->question->id);

        // get non existing object
        $crawler = $client->request('GET', '/questions/1/responses/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request('GET', '/questions/999/responses/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request('GET', '/questions/999/responses/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    public function testResponseDelete()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'DELETE', '/questions/2/responses/5.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 204);

        $result = $this->entityManager->find('MeotFormBundle:Response', 5);
        $this->assertEquals(null, $result);

        // delete non-existing object
        $crawler = $client->request(
            'DELETE', '/questions/2/responses/999.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request(
            'DELETE', '/questions/999/responses/4.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }
}
