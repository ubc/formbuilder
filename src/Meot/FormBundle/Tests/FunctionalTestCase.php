<?php

namespace Meot\FormBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

abstract class FunctionalTestCase extends WebTestCase
{
    protected $client;
    protected $entityManager;
    protected $users = array('user' => 'password', 'admin' => 'password');

    protected static function initialize()
    {
        self::createClient();
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        self::createDatabase($application);
    }

    private static function createDatabase($application)
    {
        self::executeCommand($application, "doctrine:schema:drop", array("--force" => true));
        self::executeCommand($application, "doctrine:schema:create");
        self::executeCommand($application, "doctrine:fixtures:load", array("--fixtures" => __DIR__ . "/../DataFixtures/ORM"));
    }

    private static function executeCommand($application, $command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-n"] = "";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $application->run(new ArrayInput($options));
    }

    public function setUp()
    {
        $this->populateVariables();
    }

    protected function populateVariables()
    {
        $this->client = static::createClient();
        $container = static::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
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

    protected function getClient($username = '')
    {
        if ($username != null) {
            return static::createClient(array(), array(
                'PHP_AUTH_USER' => $username,
                'PHP_AUTH_PW'   => $this->users[$username],
            ));
        } else {
            return static::createClient();
        }
    }

}
