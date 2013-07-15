<?php

namespace Meot\FormBundle\Controller;

use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Entity\Response,
    Meot\FormBundle\Form\ResponseType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template(engine="twig")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/builder")
     * @Template(engine="twig")
     */
    public function builderAction()
    {
        // force browser not to cache the builder page so that when user logout
        // and click back button, he/she will not see the previous page
        header("Cache-Control: no-store, no-cache, must-revalidate");
        return array();
    }
}
