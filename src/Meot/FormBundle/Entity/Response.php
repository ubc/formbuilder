<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Response
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Response
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
     * @ORM\Column(name="response_text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="classes", type="string", length=255, nullable=true)
     */
    private $classes;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata", type="string", length=255, nullable=true)
     */
    private $metadata;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="responses")
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
     * Set text
     *
     * @param string $text
     * @return Response
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
     * Set question
     *
     * @param \Meot\FormBundle\Entity\Question $question
     * @return Response
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

    /**
     * Set classes
     *
     * @param string $classes
     * @return Response
     */
    public function setClasses($classes)
    {
        if (is_array($classes)) {
            $this->classes = join('', $classes);
        } else {
            $this->classes = $classes;
        }

        return $this;
    }

    /**
     * Get classes
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Set metadata
     *
     * @param string $metadata
     * @return Response
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
