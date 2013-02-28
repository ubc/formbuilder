<?php
namespace Meot\FormBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Entity\Response,
    Meot\FormBundle\Form\ResponseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\Rest\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class ResponseController extends FosRestController implements ClassResourceInterface
{
    /**
     * Get all responses for question
     *
     * @ApiDoc(
     *  output="Meot\FormBundle\Entity\Response",
     *  statusCodes={
     *      200="Returned when successful"},
     *  filters={
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     * @Rest\View()
     *
     * @return ArrayCollection list of questions
     */
    public function cgetAction(Question $question)
    {
        return $question->getResponses();
    }

    /**
     * Add responses to question
     *
     * @param int $question_id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc()
     */
    public function cpostAction(Question $question, Request $request)
    {
        $entity = new Response();
        $form = $this->createForm(new ResponseType(), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $response = new HttpResponse();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set('Location',
                $this->generateUrl(
                    'get_question_response', array('question_id' => $question->getId(), 'id' => $entity->getId()),
                    true // absolute URL
                )
            );

            return $response;
        } else {
            $view = $this->view($form);
        }

        return $view;
    }

    /**
     * Get a response by id
     *
     * @param int $question_id question id
     * @param int $id          response id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc(
     * )
     */
    public function getAction($question_id, Response $response)
    {
        return array('response' => $response);
    }

    /**
     * Delete a response
     *
     * @param int $question_id question id
     * @param int $id          response id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc(
     * )
     */
    public function deleteAction($question_id, Response $response)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($response);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     */
    public function newAction(Question $question)
    {
        $response = new Response();

        $form = $this->createForm(new ResponseType(), $response);

        return $this->render('new.html.twig', array('form' => $form->createView()));
    }
}
