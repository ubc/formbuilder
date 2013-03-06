<?php
namespace Meot\FormBundle\Controller;

use Symfony\Component\HttpFoundation\Request as HttpRequest,
    Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Form\QuestionType,
    Meot\FormBundle\Entity\Form,
    Meot\FormBundle\Form\FormType,
    Meot\FormBundle\Entity\FormQuestion,
    Meot\FormBundle\Form\FormQuestionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\Rest\Util\Codes,
    FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * @RouteResource("Question")
 */
class FormQuestionController extends FosRestController implements ClassResourceInterface
{
    /**
     * Get all questions for a form
     *
     * @ApiDoc(
     *  output="Meot\FormBundle\Entity\FormQuestion",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when getting questions from non-existing form"
     *  },
     *  filters={
     *      {"name"="limit", "dataType"="integer"},
     *  }
     * )
     * @Rest\View()
     *
     * @return ArrayCollection list of form questions
     */
    public function cgetAction(Form $form)
    {
        return $form->getFormQuestions();
    }

    /**
     * Get a form question by id
     *
     * @param int $form_id     form id
     * @param int $question_id question id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc(
     * )
     */
    public function getAction($form_id, $question_id)
    {
        // check the combination of two ids
        $formQuestion = $this->getDoctrine()
            ->getRepository('MeotFormBundle:FormQuestion')
            ->getByFormAndQuestion($form_id, $question_id);
        if ($formQuestion == null) {
            throw $this->createNotFoundException('Form Question not found for form_id '.$form_id.' and question_id '.$question_id);
        }

        return $formQuestion;
    }

    /**
     * Add question to form
     *
     * @param int $form_id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc()
     */
    public function cpostAction(Form $form, HttpRequest $request)
    {
        $entity = new FormQuestion();
        $webform = $this->createForm(new FormQuestionType(), $entity);

        $webform->bind($request);

        if ($webform->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $response = new HttpResponse();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set('Location',
                $this->generateUrl(
                    'get_form_question', array('form_id' => $entity->getForm()->getId(), 'question_id' => $entity->getQuestion()->getId()),
                    true // absolute URL
                )
            );

            return $response;
        } else {
            $view = $this->view($webform);
        }

        return $view;
    }

    /**
     * Delete a form question
     *
     * @param int $form_id     form id
     * @param int $question_id form question id
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     * @ApiDoc(
     * )
     */
    public function deleteAction($form_id, $question_id)
    {
        // check the combination of two ids
        $formQuestion = $this->getDoctrine()
            ->getRepository('MeotFormBundle:FormQuestion')
            ->getByFormAndQuestion($form_id, $question_id);
        if ($formQuestion == null) {
            throw $this->createNotFoundException('Form Question not found for form_id '.$form_id.' and question_id '.$question_id);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($formQuestion);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
