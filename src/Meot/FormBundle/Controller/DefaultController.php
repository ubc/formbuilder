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
     * @Route("/")
     * @Template(engine="php")
     */
    public function indexAction()
    {
        return array();
    }
}
