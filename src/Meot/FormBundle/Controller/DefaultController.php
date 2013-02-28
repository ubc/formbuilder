<?php

namespace Meot\FormBundle\Controller;

use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Entity\Response,
    Meot\FormBundle\Form\ResponseType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/hello/{question}/new")
     * @Template()
     */
    public function newAction(Question $question)
    {
        $response = new Response();

        $form = $this->createForm(new ResponseType(), $response);

        return array('form' => $form->createView());
    }
}
