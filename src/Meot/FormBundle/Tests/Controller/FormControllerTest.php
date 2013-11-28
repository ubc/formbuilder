<?php

namespace Meot\FormBundle\Tests\Controller;

use Meot\FormBundle\Tests\FunctionalTestCase;
use FOS\RestBundle\Util\Codes;

class FormControllerTest extends FunctionalTestCase
{
    public static function setUpBeforeClass()
    {
        static::initialize();
    }

    public function testGet()
    {
        $client = $this->getClient('user');

        $crawler = $client->request('GET', '/api/forms.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $result = json_decode($response->getContent());
        $this->assertEquals(4, count($result));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals(3, $result[2]->id);
        $this->assertEquals(4, $result[3]->id);
    }

    public function testPost()
    {
        $client = $this->getClient('user');

        $crawler = $client->request(
            'POST', '/api/forms.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"name":"Form 4", "header":"header 4", "footer":"footer 4", "is_public":1, "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        // check location
        $this->assertTrue(
            $response->headers->contains('Location', 'http://localhost/api/forms/5'),
            $response->headers
        );

        // verify against database
        $forms = $this->entityManager->getRepository('MeotFormBundle:Form')->findAll();
        $this->assertEquals(5, count($forms));

        // test missing field
        $crawler = $client->request(
            'POST', '/api/forms.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"header":"header 4", "footer":"footer 4", "is_public":0, "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetObject()
    {
        $client = $this->getClient('user');

        $crawler = $client->request('GET', '/api/forms/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $form = json_decode($response->getContent());

        $expected = $this->entityManager->find('MeotFormBundle:Form', 1);
        $this->assertEquals($expected->getId(), $form->id);
        $this->assertEquals($expected->getName(), $form->name);

        // get non existing object
        $crawler = $client->request('GET', '/api/forms/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    public function testPut()
    {
        $client = $this->getClient('user');

        $crawler = $client->request(
            'PUT', '/api/forms/2.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"name":"Form 2 updated", "header":"header 2 updated", "footer":"footer 2 updated", "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NO_CONTENT);

        // verify against database
        $result = $this->entityManager->find('MeotFormBundle:Form', 2);
        $this->assertEquals(2, $result->getId());
        $this->assertEquals("Form 2 updated", $result->getName());
        $this->assertEquals("header 2 updated", $result->getHeader());
        $this->assertEquals("footer 2 updated", $result->getFooter());
        // to set boolean to false, the field should be absent from json request
        $this->assertFalse($result->getIsPublic());

        // update a non-existing object
        $client = $this->getClient('user');
        $crawler = $client->request(
            'PUT', '/api/forms/999.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"name":"Form 2 updated", "header":"header 2 updated", "footer":"footer 2 updated", "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);

        // update other's form
        $client = static::getClient('user');
        $crawler = $client->request(
            'PUT', '/api/forms/4.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"form":{"name":"Form 2 updated", "header":"header 2 updated", "footer":"footer 2 updated", "owner":2}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_FORBIDDEN);
    }

    public function testDelete()
    {
        $client = $this->getClient('user');

        $crawler = $client->request(
            'DELETE', '/api/forms/3.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NO_CONTENT);

        $result = $this->entityManager->find('MeotFormBundle:Form', 3);
        $this->assertEquals(null, $result);

        // delete non-existing object
        $crawler = $client->request(
            'DELETE', '/api/forms/999.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);

        // delete other's form
        $client = static::getClient('user');
        $crawler = $client->request(
            'DELETE', '/api/forms/4.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_FORBIDDEN);
    }

/*    public function testQuestionGet()
    {
        $client = static::getClient();

        $crawler = $client->request('GET', '/api/forms/1/questions.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $result = json_decode($response->getContent());
        $this->assertEquals(3, count($result));
        $this->assertEquals('Question 1', $result[0]->question->text);
        $this->assertEquals('Question 2', $result[1]->question->text);
        $this->assertEquals('Question 3', $result[2]->question->text);

        // try to get responses for non-existing question
        $crawler = $client->request('GET', '/api/forms/999/questions.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

    }

    public function testQuestionGetObject()
    {
        $client = static::getClient();

        $crawler = $client->request('GET', '/api/forms/1/questions/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 200);

        $result = json_decode($response->getContent());

        $expected = $this->entityManager->find('MeotFormBundle:FormQuestion', 1);
        $this->assertEquals($expected->getId(), $result->id);
        $this->assertEquals($expected->getQuestionId(), $result->question_id);
        $this->assertEquals($expected->getFormId(), $result->form_id);
        $this->assertEquals($expected->getOrder(), $result->order);

        // get non existing object
        $crawler = $client->request('GET', '/api/forms/1/questions/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request('GET', '/api/forms/999/questions/1.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request('GET', '/api/forms/999/questions/999.json');
        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    public function testQuestionPost()
    {
        $client = static::getClient();
        $crawler = $client->request(
            'POST', '/api/forms/2/questions.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"form":2, "question":3, "order":3}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 201);

        // verify against database
        $result = $this->entityManager->getRepository('MeotFormBundle:Form')->find(2);
        $this->assertEquals(3, count($result->getFormQuestions()));

        // test post response to non-existing question
        $crawler = $client->request(
            'POST', '/api/forms/999/questions.json', array(), array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"question":{"form":999, "question":3, "order":3}}'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
        // response should not be inserted
        $result = $this->entityManager->getRepository('MeotFormBundle:Form')->find(999);
        $this->assertEquals(null, $result);
    }

    public function testQuestionDelete()
    {
        $client = static::getClient();
        $crawler = $client->request(
            'DELETE', '/api/forms/2/questions/2.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 204);

        $result = $this->entityManager->find('MeotFormBundle:FormQuestion', 5);
        $this->assertEquals(null, $result);

        // delete non-existing object
        $crawler = $client->request(
            'DELETE', '/api/forms/2/questions/999.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);

        $crawler = $client->request(
            'DELETE', '/api/forms/999/questions/4.json'
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, 404);
    }*/

}
