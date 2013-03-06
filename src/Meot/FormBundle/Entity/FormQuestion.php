<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormQuestion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Meot\FormBundle\Entity\FormQuestionRepository")
 */
class FormQuestion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="form_id", type="integer")
     */
    private $form_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_id", type="integer")
     */
    private $question_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="form_questions")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="form_questions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set form_id
     *
     * @param integer $formId
     * @return FormQuestion
     */
    public function setFormId($formId)
    {
        $this->form_id = $formId;

        return $this;
    }

    /**
     * Get form_id
     *
     * @return integer
     */
    public function getFormId()
    {
        return $this->form_id;
    }

    /**
     * Set question_id
     *
     * @param integer $questionId
     * @return FormQuestion
     */
    public function setQuestionId($questionId)
    {
        $this->question_id = $questionId;

        return $this;
    }

    /**
     * Get question_id
     *
     * @return integer
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return FormQuestion
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set form
     *
     * @param \Meot\FormBundle\Entity\Form $form
     * @return FormQuestion
     */
    public function setForm(\Meot\FormBundle\Entity\Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Meot\FormBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set question
     *
     * @param \Meot\FormBundle\Entity\Question $questions
     * @return FormQuestion
     */
    public function setQuestion(\Meot\FormBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Meot\FormBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }
}