<?php
namespace Meot\FormBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Form\QuestionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class QuestionController extends FosRestController implements ClassResourceInterface
{
    /**
     * Get all questions
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={
     *      200="Returned when successful"},
     *  filters={
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     * @Rest\View(templateVar="questions")
     *
     * @return ArrayCollection list of questions
     */
    public function cgetAction()
    {
        $questions = $this->getDoctrine()
            ->getRepository('MeotFormBundle:Question')
            ->findAll();

        return $questions;
    }

    /**
     * Get question by id
     *
     * @param int $id id of the question
     *
     * @access public
     *
     * @return question object

     * @ApiDoc(
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when no question found"}
     * )
     *
     * @Rest\View
     */
    public function getAction($id)
    {
        $question = $this->getDoctrine()
            ->getRepository('MeotFormBundle:Question')
            ->find($id);

        if (!$question instanceof Question) {
            throw new NotFoundHttpException('Question not found');
        }

        return $question;
    }


    /**
     * Display the form
     *
     * @return Form form instance
     *
     * @Rest\View()
     */
    public function newAction()
    {
        return $this->getForm();
    }

    /**
     * Create a new resource
     *
     * When successful, the new resource URI will be return in header as
     * Location. E.g., Location: http://example.com/question/ID.json
     *
     * @param Request $request
     *
     * @return View view instance
     *
     * @Rest\View()
     * @ApiDoc(
     *  input="Meot\FormBundle\Form\QuestionType",
     *  statusCodes={
     *      201="Returned when successful"
     *  }
     * )
     */
    public function cpostAction(Request $request)
    {
        $entity = new Question();
        $form = $this->createForm(new QuestionType(), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $entity->setOwner($this->getUser()->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set('Location',
                $this->generateUrl(
                    'get_question', array('id' => $entity->getId()),
                    true // absolute URL
                )
            );

            return $response;
        } else {
            $view = $this->view($form);
        }

        return $view;
    }

    protected function getForm($question = null)
    {
        return $this->createForm(new QuestionType(), $question);
    }

    /**
     * Update the question
     *
     * @param Request  $request request
     * @param Question $entity  the question entity
     *
     * @return View view instance
     *
     * @Route("/questions/{id}.{_format}", name="put_question", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("PUT")
     * @Rest\View()
     * @ApiDoc(
     *  input="Meot\FormBundle\Form\QuestionType",
     *  statusCodes={
     *      204="Returned when successful",
     *      403="Access denied when the owner is not current logged user",
     *      404="Returned when no question found"}
     * )
     */
    public function putAction(Request $request, Question $entity)
    {
        $owner = $entity->getOwner();

        // check the owner
        if ($this->getUser()->getId() != $owner) {
            return $this->view(null, Codes::HTTP_FORBIDDEN);
        }

        $form = $this->getForm($entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->setOwner($owner);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Delete the question
     *
     * @param int $id id of the resource
     *
     * @return View view instance
     *
     * @Route("/questions/{id}.{_format}", name="delete_question", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("DELETE")
     * @Rest\View()
     * @ApiDoc(
     *  statusCodes={
     *      204="Returned when successful",
     *      404="Returned when no question found"}
     * )
     */
    public function deleteAction(Question $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Copy a question
     *
     * @param int $id id of the resource
     *
     * @return View view instance
     *
     * @Route("/questions/{id}/copy.{_format}", name="copy_question", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("POST")
     * @Rest\View()
     * @ApiDoc(
     *  statusCodes={
     *      204="Returned when successful",
     *      404="Returned when no question found"}
     * )
     */
    public function copyAction(Question $entity)
    {
        $newEntity = clone $entity;

        // copied questions are not master questions
        $newEntity->setIsPublic(false);
        $newEntity->setIsMaster(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newEntity);
        $em->flush();

        return $newEntity;
    }
}
