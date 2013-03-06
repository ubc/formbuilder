<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Form
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Form
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="text")
     */
    private $header;

    /**
     * @var string
     *
     * @ORM\Column(name="footer", type="text")
     */
    private $footer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $is_public;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner", type="integer")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="FormQuestion", mappedBy="form")
     **/
    private $form_questions;

    public function __construct()
    {
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
     * Set name
     *
     * @param string $name
     * @return Form
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set header
     *
     * @param string $header
     * @return Form
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set footer
     *
     * @param string $footer
     * @return Form
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Get footer
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set is_public
     *
     * @param boolean $isPublic
     * @return Form
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
     * Set owner
     *
     * @param integer $owner
     * @return Form
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
     * Add form_questions
     *
     * @param \Meot\FormBundle\Entity\FormQuestion $formQuestions
     * @return Form
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
        return $this->name;
    }
}