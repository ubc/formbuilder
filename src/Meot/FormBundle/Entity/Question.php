<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Exclude;

/**
 * Question
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Question
{
    /**
     * the id of the question
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Question prompt text
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * Response type
     * @var integer
     *
     * @ORM\Column(name="response_type", type="smallint")
     */
    private $response_type;

    /**
     * @ORM\OneToMany(targetEntity="Response", mappedBy="question")
     * @Exclude
     */
    private $responses;

    /**
     * if the question is public or private
     * @var boolean
     *
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $is_public;

    /**
     * if the question is master question, or otherwise can be edited
     * @var boolean
     *
     * @ORM\Column(name="is_master", type="boolean")
     */
    private $is_master;

    /**
     * the owner id of the question
     * @var integer
     *
     * @ORM\Column(name="owner", type="integer")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="FormQuestion", mappedBy="questions")
     * @Exclude
     **/
    private $form_questions;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
        $this->form_questions = new ArrayCollection();
    }

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
     * Set text
     *
     * @param string $text
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set response_type
     *
     * @param integer $responseType
     * @return Question
     */
    public function setResponseType($responseType)
    {
        $this->response_type = $responseType;

        return $this;
    }

    /**
     * Get response_type
     *
     * @return integer
     */
    public function getResponseType()
    {
        return $this->response_type;
    }

    /**
     * Set is_public
     *
     * @param boolean $isPublic
     * @return Question
     */
    public function setIsPublic($isPublic)
    {
        $this->is_public = $isPublic;

        return $this;
    }

    /**
     * Get is_public
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->is_public;
    }

    /**
     * Set is_master
     *
     * @param boolean $isMaster
     * @return Question
     */
    public function setIsMaster($isMaster)
    {
        $this->is_master = $isMaster;

        return $this;
    }

    /**
     * Get is_master
     *
     * @return boolean
     */
    public function getIsMaster()
    {
        return $this->is_master;
    }

    /**
     * Set owner
     *
     * @param integer $owner
     * @return Question
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add responses
     *
     * @param \Meot\FormBundle\Entity\Response $responses
     * @return Question
     */
    public function addResponse(\Meot\FormBundle\Entity\Response $responses)
    {
        $this->responses[] = $responses;

        return $this;
    }

    /**
     * Remove responses
     *
     * @param \Meot\FormBundle\Entity\Response $responses
     */
    public function removeResponse(\Meot\FormBundle\Entity\Response $responses)
    {
        $this->responses->removeElement($responses);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Add form_questions
     *
     * @param \Meot\FormBundle\Entity\FormQuestion $formQuestions
     * @return Question
     */
    public function addFormQuestion(\Meot\FormBundle\Entity\FormQuestion $formQuestions)
    {
        $this->form_questions[] = $formQuestions;

        return $this;
    }

    /**
     * Remove form_questions
     *
     * @param \Meot\FormBundle\Entity\FormQuestion $formQuestions
     */
    public function removeFormQuestion(\Meot\FormBundle\Entity\FormQuestion $formQuestions)
    {
        $this->form_questions->removeElement($formQuestions);
    }

    /**
     * Get form_questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormQuestions()
    {
        return $this->form_questions;
    }

    public function __toString()
    {
        return $this->text;
    }
}