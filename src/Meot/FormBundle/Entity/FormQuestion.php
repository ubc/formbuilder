<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormQuestion
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\Column(name="order", type="integer")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="form_questions")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $forms;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="form_questions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $questions;

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
     * Set forms
     *
     * @param \Meot\FormBundle\Entity\Form $forms
     * @return FormQuestion
     */
    public function setForms(\Meot\FormBundle\Entity\Form $forms = null)
    {
        $this->forms = $forms;
    
        return $this;
    }

    /**
     * Get forms
     *
     * @return \Meot\FormBundle\Entity\Form 
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Set questions
     *
     * @param \Meot\FormBundle\Entity\Question $questions
     * @return FormQuestion
     */
    public function setQuestions(\Meot\FormBundle\Entity\Question $questions = null)
    {
        $this->questions = $questions;
    
        return $this;
    }

    /**
     * Get questions
     *
     * @return \Meot\FormBundle\Entity\Question 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}