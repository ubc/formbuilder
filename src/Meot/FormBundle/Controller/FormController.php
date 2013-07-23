<?php
namespace Meot\FormBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Form\QuestionType,
    Meot\FormBundle\Entity\Form,
    Meot\FormBundle\Form\FormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\Rest\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class FormController extends FosRestController implements ClassResourceInterface
{
    /**
     * Get all forms
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={
     *      200="Returned when successful"},
     *  filters={
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     * @Rest\View(templateVar="forms")
     *
     * @return ArrayCollection list of forms
     */
    public function cgetAction()
    {
        $forms = $this->getDoctrine()
            ->getRepository('MeotFormBundle:Form')
            ->findAll();

        return $forms;
    }

    /**
     * Get form by id
     *
     * @param int $id id of the form
     *
     * @access public
     *
     * @return question object

     * @ApiDoc(
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when no form found"}
     * )
     *
     * @Rest\View
     */
    public function getAction(Form $form)
    {
        return $form;
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
     *  resource=true,
     *  input="Meot\FormBundle\Form\FormType",
     *  statusCodes={
     *      201="Returned when successful"
     *  }
     * )
     */
    public function cpostAction(Request $request)
    {
        $entity = new Form();
        $form = $this->createForm(new FormType(), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $user = $this->getUser();
            //$entity->setOwner($user->getUsername());
            $entity->setOwner(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $response = new HttpResponse();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set('Location',
                $this->generateUrl(
                    'get_form', array('form' => $entity->getId()),
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
        return $this->createForm(new FormType(), $question);
    }

    /**
     * Update the form
     *
     * @param Request $request request
     * @param Form    $entity  the form entity
     *
     * @return View view instance
     *
     * @Route("/forms/{id}.{_format}", name="put_form", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("PUT")
     * @Rest\View()
     * @ApiDoc(
     *  input="Meot\FormBundle\Form\FormType",
     *  statusCodes={
     *      204="Returned when successful",
     *      403="Access denied when the owner is not current logged user",
     *      404="Returned when no form found"}
     * )
     */
    public function putAction(Request $request, Form $entity)
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
     * Delete the form
     *
     * @param int $id id of the resource
     *
     * @return View view instance
     *
     * @Route("/forms/{id}.{_format}", name="delete_form", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("DELETE")
     * @Rest\View()
     * @ApiDoc(
     *  statusCodes={
     *      204="Returned when successful",
     *      404="Returned when no form found"}
     * )
     */
    public function deleteAction(Form $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
